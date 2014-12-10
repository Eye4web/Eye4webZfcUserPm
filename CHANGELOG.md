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