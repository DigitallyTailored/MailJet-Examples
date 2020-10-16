<?php


//this is if the WordPress plugin is being used which already contains the MailJet PHP API, otherwise you can manually require it here
//require 'vendor/autoload.php';
use \Mailjet\Resources;
//getting the API keys stored against the wordpress plugin
$mj = new \Mailjet\Client(get_option('mailjet_apikey'), get_option('mailjet_apisecret'), true, ['version' => 'v3']);


$contactTest = "luke@devanew.com";

//add contact
$body = [
    'Email' => $contactTest
];
$response = $mj->post(Resources::$Contact, ['body' => $body]);
$response->success() && var_dump($response->getData());



//add contact to list
$body = [
    'IsUnsubscribed' => "false",
    'ContactAlt' => $contactTest,
    'ListID' => "12345",
];
$response = $mj->post(Resources::$Listrecipient, ['body' => $body]);
$response->success() && var_dump($response->getData());



//get contact id with email address
$response = $mj->get(\Mailjet\Resources::$Contact, ['id' => $contactTest]);
$response->success() && var_dump($response->getData());



//add contact meta data. You can use the email as an ID, or the above to get the ID first if you wish
$body = [
  'Data' => [
    [
      'Name' => "location",
      'Value' => "Ireland"
    ]
  ]
];
$response = $mj->put(\Mailjet\Resources::$Contactdata, ['id' => $contactTest, 'body' => $body]);
$response->success() && var_dump($response->getData());



//send email to list using a template. This uses API v3.1 which has a slightly different syntax so we need a new client
$mj = new \Mailjet\Client(get_option('mailjet_apikey'), get_option('mailjet_apisecret'), true, ['version' => 'v3.1']);
$body = [
    'Messages' => [
        [   'TemplateID' => 12345,
            "TemplateLanguage" => true, //required for using variables
            'From' => [
                'Email' => "marketing@example.com",
                'Name' => "Marketing Team"
            ],
            'To' => [
                [
                    'Email' => "12345@lists.mailjet.com", //this email address will send to everyone in the list
                ]
            ],
            'Subject' => "Test subject",
            'Variables' => ['api_content' => 'I am the email content!', 'api_title' => 'I am a title!'], //These variables are included in the template so we can design the email outside of PHP and set the content here
        ]
    ]
];
$response = $mj->post(Resources::$Email, ['body' => $body]);
$response->success() && var_dump($response->getData());
