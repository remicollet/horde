<?php
/**
 * The Ingo_Script_Procmail:: class represents a Procmail script generator.
 *
 * Copyright 2003-2013 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Brent J. Nordquist <bjn@horde.org>
 * @author   Ben Chavet <ben@horde.org>
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class Ingo_Script_Procmail extends Ingo_Script_Base
{
    /**
     * A list of driver features.
     *
     * @var array
     */
    protected $_features = array(
        /* Can tests be case sensitive? */
        'case_sensitive' => true,
        /* Does the driver support setting IMAP flags? */
        'imap_flags' => false,
        /* Does the driver support the stop-script option? */
        'stop_script' => true,
        /* Can this driver perform on demand filtering? */
        'on_demand' => false,
        /* Does the driver require a script file to be generated? */
        'script_file' => true,
    );

    /**
     * The list of actions allowed (implemented) for this driver.
     *
     * @var array
     */
    protected $_actions = array(
        Ingo_Storage::ACTION_KEEP,
        Ingo_Storage::ACTION_MOVE,
        Ingo_Storage::ACTION_DISCARD,
        Ingo_Storage::ACTION_REDIRECT,
        Ingo_Storage::ACTION_REDIRECTKEEP,
        Ingo_Storage::ACTION_REJECT
    );

    /**
     * The categories of filtering allowed.
     *
     * @var array
     */
    protected $_categories = array(
        Ingo_Storage::ACTION_BLACKLIST,
        Ingo_Storage::ACTION_WHITELIST,
        Ingo_Storage::ACTION_VACATION,
        Ingo_Storage::ACTION_FORWARD
    );

    /**
     * The types of tests allowed (implemented) for this driver.
     *
     * @var array
     */
    protected $_types = array(
        Ingo_Storage::TYPE_HEADER,
        Ingo_Storage::TYPE_BODY
    );

    /**
     * A list of any special types that this driver supports.
     *
     * @var array
     */
    protected $_special_types = array(
        'Destination',
    );

    /**
     * The list of tests allowed (implemented) for this driver.
     *
     * @var array
     */
    protected $_tests = array(
        'contains',
        'not contain',
        'begins with',
        'not begins with',
        'ends with',
        'not ends with',
        'regex'
    );

    /**
     * Constructor.
     *
     * @param array $params  A hash containing parameters needed.
     */
    public function __construct($params = array())
    {
        parent::__construct($params);

        // Bug #10113: Need to explicitly define these variables instead of
        // relying on system defaults.
        if ($this->_params['path_style'] == 'maildir') {
            if (!isset($this->_params['variables']['DEFAULT'])) {
                $this->_params['variables']['DEFAULT'] = '$HOME/Maildir/';
            }
            if (!isset($this->_params['variables']['MAILDIR'])) {
                $this->_params['variables']['MAILDIR'] = '$HOME/Maildir';
            }
        }
    }

    /**
     * Generates the procmail scripts to do the filtering specified in the
     * rules.
     */
    protected function _generate()
    {
        $filters = $this->_params['storage']
             ->retrieve(Ingo_Storage::ACTION_FILTERS);

        $this->_addItem(Ingo::RULE_ALL, new Ingo_Script_Procmail_Comment(_("procmail script generated by Ingo") . ' (' . date('F j, Y, g:i a') . ')'));

        if (isset($this->_params['forward_file']) &&
            isset($this->_params['forward_string'])) {
            $this->_addItem(
                Ingo::RULE_ALL,
                new Ingo_Script_String($this->_params['forward_string']),
                $this->_params['forward_file']
            );
        }

        /* Add variable information, if present. */
        if (!empty($this->_params['variables']) &&
            is_array($this->_params['variables'])) {
            foreach ($this->_params['variables'] as $key => $val) {
                $this->_addItem(Ingo::RULE_ALL, new Ingo_Script_Procmail_Variable(array('name' => $key, 'value' => $val)));
            }
        }

        foreach ($filters->getFilterList($this->_params['skip']) as $filter) {
            switch ($filter['action']) {
            case Ingo_Storage::ACTION_BLACKLIST:
                $this->generateBlacklist(!empty($filter['disable']));
                break;

            case Ingo_Storage::ACTION_WHITELIST:
                $this->generateWhitelist(!empty($filter['disable']));
                break;

            case Ingo_Storage::ACTION_VACATION:
                $this->generateVacation(!empty($filter['disable']));
                break;

            case Ingo_Storage::ACTION_FORWARD:
                $this->generateForward(!empty($filter['disable']));
                break;

            default:
                if (in_array($filter['action'], $this->_actions)) {
                    /* Create filter if using AND. */
                    if ($filter['combine'] == Ingo_Storage::COMBINE_ALL) {
                        $recipe = new Ingo_Script_Procmail_Recipe($filter, $this->_params);
                        if (!$filter['stop']) {
                            $recipe->addFlag('c');
                        }
                        foreach ($filter['conditions'] as $condition) {
                            $recipe->addCondition($condition);
                        }
                        $this->_addItem(Ingo::RULE_FILTER, new Ingo_Script_Procmail_Comment($filter['name'], !empty($filter['disable']), true));
                        $this->_addItem(Ingo::RULE_FILTER, $recipe);
                    } else {
                        /* Create filter if using OR */
                        $this->_addItem(Ingo::RULE_FILTER, new Ingo_Script_Procmail_Comment($filter['name'], !empty($filter['disable']), true));
                        $loop = 0;
                        foreach ($filter['conditions'] as $condition) {
                            $recipe = new Ingo_Script_Procmail_Recipe($filter, $this->_params);
                            if ($loop++) {
                                $recipe->addFlag('E');
                            }
                            if (!$filter['stop']) {
                                $recipe->addFlag('c');
                            }
                            $recipe->addCondition($condition);
                            $this->_addItem(Ingo::RULE_FILTER, $recipe);
                        }
                    }
                }
            }
        }

        // If an external delivery program is used, add final rule
        // to deliver to $DEFAULT
        if (isset($this->_params['delivery_agent'])) {
            $this->_addItem(Ingo::RULE_FILTER, new Ingo_Script_Procmail_Default($this->_params));
        }
    }

    /**
     * Generates the procmail script to handle the blacklist specified in
     * the rules.
     *
     * @param boolean $disable  Disable the blacklist?
     */
    public function generateBlacklist($disable = false)
    {
        $blacklist = $this->_params['storage']
             ->retrieve(Ingo_Storage::ACTION_BLACKLIST);
        $bl_addr = $blacklist->getBlacklist();
        $bl_folder = $blacklist->getBlacklistFolder();

        $bl_type = empty($bl_folder)
            ? Ingo_Storage::ACTION_DISCARD
            : Ingo_Storage::ACTION_MOVE;

        if (!empty($bl_addr)) {
            $this->_addItem(Ingo::RULE_BLACKLIST, new Ingo_Script_Procmail_Comment(_("Blacklisted Addresses"), $disable, true));
            $params = array('action-value' => $bl_folder,
                            'action' => $bl_type,
                            'disable' => $disable);

            foreach ($bl_addr as $address) {
                if (!empty($address)) {
                    $recipe = new Ingo_Script_Procmail_Recipe($params, $this->_params);
                    $recipe->addCondition(array('field' => 'From', 'value' => $address, 'match' => 'address'));
                    $this->_addItem(Ingo::RULE_BLACKLIST, $recipe);
                }
            }
        }
    }

    /**
     * Generates the procmail script to handle the whitelist specified in
     * the rules.
     *
     * @param boolean $disable  Disable the whitelist?
     */
    public function generateWhitelist($disable = false)
    {
        $whitelist = $this->_params['storage']
             ->retrieve(Ingo_Storage::ACTION_WHITELIST);
        $wl_addr = $whitelist->getWhitelist();

        if (!empty($wl_addr)) {
            $this->_addItem(Ingo::RULE_WHITELIST, new Ingo_Script_Procmail_Comment(_("Whitelisted Addresses"), $disable, true));
            foreach ($wl_addr as $address) {
                if (!empty($address)) {
                    $recipe = new Ingo_Script_Procmail_Recipe(array('action' => Ingo_Storage::ACTION_KEEP, 'disable' => $disable), $this->_params);
                    $recipe->addCondition(array('field' => 'From', 'value' => $address, 'match' => 'address'));
                    $this->_addItem(Ingo::RULE_WHITELIST, $recipe);
                }
            }
        }
    }

    /**
     * Generates the procmail script to handle vacation.
     *
     * @param boolean $disable  Disable vacation?
     */
    public function generateVacation($disable = false)
    {
        $vacation = $this->_params['storage']
             ->retrieve(Ingo_Storage::ACTION_VACATION);
        $addresses = $vacation->getVacationAddresses();
        $actionval = array(
            'addresses' => $addresses,
            'subject' => $vacation->getVacationSubject(),
            'days' => $vacation->getVacationDays(),
            'reason' => $vacation->getVacationReason(),
            'ignorelist' => $vacation->getVacationIgnorelist(),
            'excludes' => $vacation->getVacationExcludes(),
            'start' => $vacation->getVacationStart(),
            'end' => $vacation->getVacationEnd(),
        );

        if (!empty($addresses)) {
            $this->_addItem(Ingo::RULE_VACATION, new Ingo_Script_Procmail_Comment(_("Vacation"), $disable, true));
            $params = array('action' => Ingo_Storage::ACTION_VACATION,
                            'action-value' => $actionval,
                            'disable' => $disable);
            $recipe = new Ingo_Script_Procmail_Recipe($params, $this->_params);
            $this->_addItem(Ingo::RULE_VACATION, $recipe);
        }
    }

    /**
     * Generates the procmail script to handle mail forwards.
     *
     * @param boolean $disable  Disable forwarding?
     */
    public function generateForward($disable = false)
    {
        $forward = $this->_params['storage']
             ->retrieve(Ingo_Storage::ACTION_FORWARD);
        $addresses = $forward->getForwardAddresses();

        if (!empty($addresses)) {
            $this->_addItem(Ingo::RULE_FORWARD, new Ingo_Script_Procmail_Comment(_("Forwards"), $disable, true));
            $params = array('action' => Ingo_Storage::ACTION_FORWARD,
                            'action-value' => $addresses,
                            'disable' => $disable);
            $recipe = new Ingo_Script_Procmail_Recipe($params, $this->_params);
            if ($forward->getForwardKeep()) {
                $recipe->addFlag('c');
            }
            $this->_addItem(Ingo::RULE_FORWARD, $recipe);
        }
    }
}
