<?php
/**
 * Unit tests for Horde_ActiveSync_Folder_Imap
 *
 * @author Michael J. Rubinsky <mrubinsk@horde.org>
 * @category Horde
 * @package ActiveSync
 */
class Horde_ActiveSync_ImapAdapterTest extends Horde_Test_Case
{
    public function testBug13711()
    {
        $this->markTestIncomplete("Useless test without all the fixtures.");
        $factory = new Horde_ActiveSync_Factory_TestServer();
        $imap_client = $this->getMockSkipConstructor('Horde_Imap_Client_Socket');
        $imap_client->expects($this->any())
            ->method('fetch')
            ->will($this->_getFixturesFor13711());

        $imap_factory = new Horde_ActiveSync_Stub_ImapFactory();
        $imap_factory->fixture = $imap_client;
        $adapter = new Horde_ActiveSync_Imap_Adapter(array('factory' => $imap_factory));

        $adapter->getMessages(
            'INBOX',
            array(462),
            array(
                'protocolversion' => 14.1,
                'bodyprefs' => array(
                    'wanted' => Horde_ActiveSync::BODYPREF_TYPE_MIME,
                     Horde_ActiveSync::BODYPREF_TYPE_MIME => array(
                           'type' => Horde_ActiveSync::BODYPREF_TYPE_MIME,
                           'truncationsize' => 200000)
                ),
                'mimesupport' => Horde_ActiveSync::MIME_SUPPORT_ALL,
            )
        );
    }

    protected function _getFixturesFor13711()
    {
        $first = "TzozMToiSG9yZGVfSW1hcF9DbGllbnRfRmV0Y2hfUmVzdWx0cyI6Mzp7czo4OiIAKgBfZGF0YSI7YToxOntpOjQ2MjtPOjI4OiJIb3JkZV9JbWFwX0NsaWVudF9EYXRhX0ZldGNoIjoxOntzOjg6IgAqAF9kYXRhIjthOjY6e2k6MTQ7aToxNDtpOjEzO2k6NDYyO2k6MTA7YTowOnt9aToxO0M6MTU6IkhvcmRlX01pbWVfUGFydCI6MzA5OnthOjIwOntpOjA7aToxO2k6MTtzOjQ6InRleHQiO2k6MjtzOjg6ImNhbGVuZGFyIjtpOjM7czo0OiI4Yml0IjtpOjQ7YToxOntpOjA7czoyOiJkZSI7fWk6NTtzOjA6IiI7aTo2O3M6MDoiIjtpOjc7YToxOntzOjQ6InNpemUiO3M6NDoiMTUyMyI7fWk6ODthOjI6e3M6NzoiY2hhcnNldCI7czo1OiJVVEYtOCI7czo2OiJtZXRob2QiO3M6NzoiUkVRVUVTVCI7fWk6OTthOjA6e31pOjEwO3M6MToiMSI7aToxMTtzOjE6IgoiO2k6MTI7YTowOnt9aToxMztOO2k6MTQ7aToxNTIzO2k6MTU7TjtpOjE2O047aToxNztiOjA7aToxODtiOjA7aToxOTtOO319aTo5O0M6MzE6IkhvcmRlX0ltYXBfQ2xpZW50X0RhdGFfRW52ZWxvcGUiOjE2Mzg6e2E6Mjp7czoxOiJkIjtDOjE4OiJIb3JkZV9NaW1lX0hlYWRlcnMiOjE1Nzk6e2E6Mzp7aTowO2k6MztpOjE7YTo1OntzOjQ6IkRhdGUiO086MjM6IkhvcmRlX01pbWVfSGVhZGVyc19EYXRlIjoyOntzOjg6IgAqAF9uYW1lIjtzOjQ6IkRhdGUiO3M6MTA6IgAqAF92YWx1ZXMiO2E6MTp7aTowO3M6MzA6IkZyaSwgNyBOb3YgMjAxNCAxMzozNjo1NCArMDEwMCI7fX1zOjc6IlN1YmplY3QiO086MjY6IkhvcmRlX01pbWVfSGVhZGVyc19TdWJqZWN0IjoyOntzOjg6IgAqAF9uYW1lIjtzOjc6IlN1YmplY3QiO3M6MTA6IgAqAF92YWx1ZXMiO2E6MTp7aTowO3M6Mzk6Ik1BQyA+IEZhaHJzdHVobCA+IEJlc2NocmlmdHVuZyA+IE11c3RlciI7fX1zOjQ6ImZyb20iO086Mjg6IkhvcmRlX01pbWVfSGVhZGVyc19BZGRyZXNzZXMiOjM6e3M6MTE6ImFwcGVuZF9hZGRyIjtiOjE7czo4OiIAKgBfbmFtZSI7czo0OiJmcm9tIjtzOjEwOiIAKgBfdmFsdWVzIjtDOjIyOiJIb3JkZV9NYWlsX1JmYzgyMl9MaXN0IjoxNzQ6e2E6MTp7aTowO086MjU6IkhvcmRlX01haWxfUmZjODIyX0FkZHJlc3MiOjQ6e3M6NzoiY29tbWVudCI7YTowOnt9czo3OiJtYWlsYm94IjtzOjEyOiJtYXJpby5sb3JlbnoiO3M6ODoiACoAX2hvc3QiO3M6MTA6ImRlc2VydmUuZGUiO3M6MTI6IgAqAF9wZXJzb25hbCI7czoxMjoiTWFyaW8gTG9yZW56Ijt9fX19czoyOiJ0byI7TzoyODoiSG9yZGVfTWltZV9IZWFkZXJzX0FkZHJlc3NlcyI6Mzp7czoxMToiYXBwZW5kX2FkZHIiO2I6MTtzOjg6IgAqAF9uYW1lIjtzOjI6InRvIjtzOjEwOiIAKgBfdmFsdWVzIjtDOjIyOiJIb3JkZV9NYWlsX1JmYzgyMl9MaXN0Ijo2MDA6e2E6Mzp7aTowO086MjU6IkhvcmRlX01haWxfUmZjODIyX0FkZHJlc3MiOjQ6e3M6NzoiY29tbWVudCI7YTowOnt9czo3OiJtYWlsYm94IjtzOjE2OiJwYXRyaWNrLmxvZXNjaGVyIjtzOjg6IgAqAF9ob3N0IjtzOjEzOiJiaWxmaW5nZXIuY29tIjtzOjEyOiIAKgBfcGVyc29uYWwiO3M6NTY6IidMw7ZzY2hlciwgUGF0cmljayAoQmlsZmluZ2VyIFJlYWwgRXN0YXRlIEFyZ29uZW8gR21iSCknIjt9aToxO086MjU6IkhvcmRlX01haWxfUmZjODIyX0FkZHJlc3MiOjQ6e3M6NzoiY29tbWVudCI7YTowOnt9czo3OiJtYWlsYm94IjtzOjEzOiJncmVnb3IuYm90enVtIjtzOjg6IgAqAF9ob3N0IjtzOjEzOiJiaWxmaW5nZXIuY29tIjtzOjEyOiIAKgBfcGVyc29uYWwiO3M6NTM6IidCb3R6dW0sIEdyZWdvciAoQmlsZmluZ2VyIFJlYWwgRXN0YXRlIEFyZ29uZW8gR21iSCknIjt9aToyO086MjU6IkhvcmRlX01haWxfUmZjODIyX0FkZHJlc3MiOjQ6e3M6NzoiY29tbWVudCI7YTowOnt9czo3OiJtYWlsYm94IjtzOjQ6InJ1cHAiO3M6ODoiACoAX2hvc3QiO3M6MTE6ImluZGl0ZWMuY29tIjtzOjEyOiIAKgBfcGVyc29uYWwiO3M6MTQ6IidGbG9yaWFuIFJ1cHAnIjt9fX19czoxMDoiTWVzc2FnZS1JRCI7TzoyODoiSG9yZGVfTWltZV9IZWFkZXJzX01lc3NhZ2VpZCI6Mjp7czo4OiIAKgBfbmFtZSI7czoxMDoiTWVzc2FnZS1JRCI7czoxMDoiACoAX3ZhbHVlcyI7YToxOntpOjA7czo0NDoiPDAwOTEwMWNmZmE4NyQ4MzhlOGM0MCQ4YWFiYTRjMCRAZGVzZXJ2ZS5kZT4iO319fWk6MjtzOjE6IgoiO319czoxOiJ2IjtpOjM7fX1pOjM7YToxOntpOjA7czo3MDA6IkZyb206IE1hcmlvIExvcmVueiA8bWFyaW8ubG9yZW56QGRlc2VydmUuZGU+DQpUbzogIj0/dXRmLTg/Yj9KMHpEdG5OamFHVnlMQT09Pz0gUGF0cmljayAoQmlsZmluZ2VyIFJlYWwgRXN0YXRlIEFyZ29uZW8NCiBHbWJIKSciIDxwYXRyaWNrLmxvZXNjaGVyQGJpbGZpbmdlci5jb20+LCAiJ0JvdHp1bSwgR3JlZ29yIChCaWxmaW5nZXIgUmVhbA0KIEVzdGF0ZSBBcmdvbmVvIEdtYkgpJyIgPGdyZWdvci5ib3R6dW1AYmlsZmluZ2VyLmNvbT4sICdGbG9yaWFuIFJ1cHAnDQogPHJ1cHBAaW5kaXRlYy5jb20+DQpTdWJqZWN0OiBNQUMgPiBGYWhyc3R1aGwgPiBCZXNjaHJpZnR1bmcgPiBNdXN0ZXINCkRhdGU6IEZyaSwgNyBOb3YgMjAxNCAxMzozNjo1NCArMDEwMA0KTWVzc2FnZS1JRDogPDAwOTEwMWNmZmE4NyQ4MzhlOGM0MCQ4YWFiYTRjMCRAZGVzZXJ2ZS5kZT4NCk1JTUUtVmVyc2lvbjogMS4wDQpDb250ZW50LVR5cGU6IHRleHQvY2FsZW5kYXI7IGNoYXJzZXQ9VVRGLTg7IG1ldGhvZD1SRVFVRVNUDQpDb250ZW50LVRyYW5zZmVyLUVuY29kaW5nOiA4Yml0DQpYLU1haWxlcjogTWljcm9zb2Z0IE91dGxvb2sgMTUuMA0KVGhyZWFkLUluZGV4OiBBYy82aDRLMDhSa0Q3UndYUnUyMkprczA1ZHNuTlFBQUFBUFENCkNvbnRlbnQtTGFuZ3VhZ2U6IGRlDQpVc2VyLUFnZW50OiBIb3JkZSBBcHBsaWNhdGlvbiBGcmFtZXdvcmsgNQ0KDQoiO319fX1zOjExOiIAKgBfa2V5VHlwZSI7aToyO3M6MTE6IgAqAF9vYkNsYXNzIjtzOjI4OiJIb3JkZV9JbWFwX0NsaWVudF9EYXRhX0ZldGNoIjt9";
        //$first_obj = unserialize(base64_decode($first));

        $second = "TzozMToiSG9yZGVfSW1hcF9DbGllbnRfRmV0Y2hfUmVzdWx0cyI6Mzp7czo4OiIAKgBfZGF0YSI7YToxOntpOjQ2MjtPOjI4OiJIb3JkZV9JbWFwX0NsaWVudF9EYXRhX0ZldGNoIjoxOntzOjg6IgAqAF9kYXRhIjthOjI6e2k6MTQ7aToxNDtpOjEzO2k6NDYyO319fXM6MTE6IgAqAF9rZXlUeXBlIjtpOjI7czoxMToiACoAX29iQ2xhc3MiO3M6Mjg6IkhvcmRlX0ltYXBfQ2xpZW50X0RhdGFfRmV0Y2giO30=";
        $second_obj = unserialize(base64_decode($second));

        // $third = "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AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID////////////////////////AwMDAwMDAwMD///////////////////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID////////////////////AwMAAAIAAAIDAwMDAwMD///////////////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID////////////////AwMAAAIAAAIAAAIAAAIDAwMDAwMD///////////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID////////////AwMAAAIAAAIAAAIAAAIAAAIAAAIDAwMD///////////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID////////AwMAAAIAAAIAAAIAAAIAAAIAAAIAAAICAgIDAwMD///////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////8AAIAAAIAAAIAAAID///+AgIAAAIAAAIAAAIDAwMDAwMD///////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////8AAIAAAIAAAID////////////AwMAAAIAAAIAAAIDAwMDAwMD///////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////8AAID////////////////////AwMAAAIAAAICAgIDAwMD///////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID////////////////////////////////////////AwMAAAIAAAICAgIDAwMD///////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////8AAIAAAIDAwMDAwMD///////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////8AAIAAAIDAwMDAwMD///////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////8AAIAAAIDAwMDAwMD///////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////////8AAIAAAIDAwMD///////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////////////8AAIAAAID///////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////////////////8AAIDAwMD///+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////////////////////8AAID///+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////////////////////////////////////////////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADAwMD///////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////+AgICAgICAgICAgICAgICAgICAgICAgICAgIDAwMAAAADAwMD///////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgICAgICAgICAgICAgICAgID///+AgICAgICAgICAgICAgICAgICAgIDAwMAAAACAgICAgICAgICAgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID////AwMDAwMDAwMDAwMDAwMDAwMCAgIDAwMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAgID///////////////////////+AgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA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";
        // $third_obj = unserialize(base64_decode($third));

        $fourth = "TzozMToiSG9yZGVfSW1hcF9DbGllbnRfRmV0Y2hfUmVzdWx0cyI6Mzp7czo4OiIAKgBfZGF0YSI7YToxOntpOjQ2MjtPOjI4OiJIb3JkZV9JbWFwX0NsaWVudF9EYXRhX0ZldGNoIjoxOntzOjg6IgAqAF9kYXRhIjthOjM6e2k6MTQ7aToxNDtpOjEzO2k6NDYyO2k6NjthOjE6e2k6MTthOjI6e3M6MToiZCI7czo0OiI4Yml0IjtzOjE6InQiO3M6MTUyMzoiQkVHSU46VkNBTEVOREFSDQpQUk9ESUQ6LS8vTWljcm9zb2Z0IENvcnBvcmF0aW9uLy9PdXRsb29rIDE1LjAgTUlNRURJUi8vRU4NClZFUlNJT046Mi4wDQpNRVRIT0Q6UkVRVUVTVA0KWC1NUy1PTEstRk9SQ0VJTlNQRUNUT1JPUEVOOlRSVUUNCkJFR0lOOlZUSU1FWk9ORQ0KVFpJRDpXLiBFdXJvcGUgU3RhbmRhcmQgVGltZQ0KQkVHSU46U1RBTkRBUkQNCkRUU1RBUlQ6MTYwMTEwMjhUMDMwMDAwDQpSUlVMRTpGUkVRPVlFQVJMWTtCWURBWT0tMVNVO0JZTU9OVEg9MTANClRaT0ZGU0VURlJPTTorMDIwMA0KVFpPRkZTRVRUTzorMDEwMA0KRU5EOlNUQU5EQVJEDQpCRUdJTjpEQVlMSUdIVA0KRFRTVEFSVDoxNjAxMDMyNVQwMjAwMDANClJSVUxFOkZSRVE9WUVBUkxZO0JZREFZPS0xU1U7QllNT05USD0zDQpUWk9GRlNFVEZST006KzAxMDANClRaT0ZGU0VUVE86KzAyMDANCkVORDpEQVlMSUdIVA0KRU5EOlZUSU1FWk9ORQ0KQkVHSU46VkVWRU5UDQpBVFRFTkRFRTtDTj0iTMO2c2NoZXIsIFBhdHJpY2sgKEJpbGZpbmdlciBSZWFsIEVzdGF0ZSBBcmdvbmVvIEdtYkgpIjtSU1ZQPVQNCglSVUU6bWFpbHRvOnBhdHJpY2subG9lc2NoZXJAYmlsZmluZ2VyLmNvbQ0KQVRURU5ERUU7Q049IidCb3R6dW0sIEdyZWdvciAoQmlsZmluZ2VyIFJlYWwgRXN0YXRlIEFyZ29uZW8gR21iSCknIjtSU1ZQPVRSDQoJVUU6bWFpbHRvOmdyZWdvci5ib3R6dW1AYmlsZmluZ2VyLmNvbQ0KQVRURU5ERUU7Q049IkZsb3JpYW4gUnVwcCI7UlNWUD1UUlVFOm1haWx0bzpydXBwQGluZGl0ZWMuY29tDQpDTEFTUzpQVUJMSUMNCkNSRUFURUQ6MjAxNDExMDdUMTIzNjUzWg0KREVTQ1JJUFRJT046XG5cbg0KRFRFTkQ7VFpJRD0iVy4gRXVyb3BlIFN0YW5kYXJkIFRpbWUiOjIwMTQxMTEwVDEzMzAwMA0KRFRTVEFNUDoyMDE0MTEwN1QxMjM2NTNaDQpEVFNUQVJUO1RaSUQ9IlcuIEV1cm9wZSBTdGFuZGFyZCBUaW1lIjoyMDE0MTExMFQxMjAwMDANCkxBU1QtTU9ESUZJRUQ6MjAxNDExMDdUMTIzNjUzWg0KTE9DQVRJT046TUFDDQpPUkdBTklaRVI7Q049Ik1hcmlvIExvcmVueiI6bWFpbHRvOm1hcmlvLmxvcmVuekBkZXNlcnZlLmRlDQpQUklPUklUWTo1DQpTRVFVRU5DRTowDQpTVU1NQVJZO0xBTkdVQUdFPWRlOk1BQyA+IEZhaHJzdHVobCA+IEJlc2NocmlmdHVuZyA+IE11c3Rlcg0KVFJBTlNQOk9QQVFVRQ0KVUlEOjA0MDAwMDAwODIwMEUwMDA3NEM1QjcxMDFBODJFMDA4MDAwMDAwMDBCMDU2RjY2QThGRkFDRjAxMDAwMDAwMDAwMDAwMDAwDQoJMDEwMDAwMDAwNzk4QjRFRDNCNENFMTk0NUFCMEEyNTJGNjhGNkE3M0YNClgtTUlDUk9TT0ZULUNETy1CVVNZU1RBVFVTOlRFTlRBVElWRQ0KWC1NSUNST1NPRlQtQ0RPLUlNUE9SVEFOQ0U6MQ0KWC1NSUNST1NPRlQtQ0RPLUlOVEVOREVEU1RBVFVTOkJVU1kNClgtTUlDUk9TT0ZULURJU0FMTE9XLUNPVU5URVI6RkFMU0UNClgtTVMtT0xLLUFVVE9TVEFSVENIRUNLOkZBTFNFDQpYLU1TLU9MSy1DT05GVFlQRTowDQpFTkQ6VkVWRU5UDQpFTkQ6VkNBTEVOREFSDQoiO319fX19czoxMToiACoAX2tleVR5cGUiO2k6MjtzOjExOiIAKgBfb2JDbGFzcyI7czoyODoiSG9yZGVfSW1hcF9DbGllbnRfRGF0YV9GZXRjaCI7fQ==";
        $fourth_obj = unserialize(base64_decode($fourth));
        //return $this->onConsecutiveCalls($first_obj, $second_obj, $fourth_obj);
        return $this->onConsecutiveCalls($second_obj, $fourth_obj);
    }

}
