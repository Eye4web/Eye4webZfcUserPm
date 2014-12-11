# Development

2014-12-9
---------
 - added <code>"rhumsaa/uuid": "2.*"</code> to composer.json because used by <code>Eye4web\ZfcUser\Pm\Entity\Conversation.</code>
 - added Entity\Conversation Test
 - added  Entity\ConversationReceiver Test
 - added  Entity\Message Test
 - exclude entity interfaces from coverage
 - added Form\NewMessageForm test
 - added Form\NewConversationForm test
 - added View\Helper\ZfcUserPmHelper test
 - added Service\PmService test ( 70 % )
 
2014-12-10
----------
- undo add setDeleted($deleted) in ConversationReceiver because already in there.
- added Form\DeleteConversationsForm test
- Service\PmService  test fully implemented
- added Factory\View\Helper\ZfcUserPmHelperFactory test
- added Factory\Service\PmServiceFactory test
- added Factory\Mapper\DoctrineORM\PmMapperFactory test
- added Factory\Options\ModuleOptionsFactory test
- added Factory\Controller\PmControllerFactory test
- added Options\ModuleOptions test
- adding Mapper/DoctrineORM/PmMapper test

2014-12-11
----------
- patch Factory\Controller\PmController test because updated code.
- update test/Bootstrap.php to support travis test and update composer.json to fix travis build by adding :
```
"zendframework/zend-serializer": "~2.1",
```
- exclude "html-report" folder from phpcs check in .travis.yml
- added
```
"zf-commons/zfc-user-doctrine-orm": "~1.0"
```
 to require-dev in composer.json

- added
 ```
 "codeclimate/php-test-reporter": "~0.*"
 ```
 to require-dev in composer.json