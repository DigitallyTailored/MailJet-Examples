<?php


//this is if the WordPress plugin is being used which already contains the MailJet PHP API, otherwise you can manually require it here
//https://en-gb.wordpress.org/plugins/mailjet-for-wordpress/
use \Mailjet\Client;
use \Mailjet\Resources;
//getting the API keys stored against the wordpress plugin
$mj = new Client(get_option('mailjet_apikey'), get_option('mailjet_apisecret'), true, ['version' => 'v3']);


$contactTest = "new-contact@example.com";

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
$response = $mj->get(Resources::$Contact, ['id' => $contactTest]);
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
$response = $mj->put(Resources::$Contactdata, ['id' => $contactTest, 'body' => $body]);
$response->success() && var_dump($response->getData());



//send email to list using a template. This uses API v3.1 which has a slightly different syntax so we need a new client
$mj = new Client(get_option('mailjet_apikey'), get_option('mailjet_apisecret'), true, ['version' => 'v3.1']);
$body = [
    'Messages' => [
        [   'TemplateID' => 12345,
            //enable template language for using variables
            "TemplateLanguage" => true,
            'From' => [
                'Email' => "marketing@example.com",
                'Name' => "Marketing Team"
            ],
            'To' => [
                [
                    //this email address will send to everyone in the list. You can get the email to use from the list page
                    'Email' => "12345@lists.mailjet.com",
                ]
            ],
            'Subject' => "Test subject",
            //These variables are included in the template so we can design the email outside of PHP and set the content here
            //in the template use {{var:api_title}} and {{var:api_content}}
            //{{var:api_content}} should be inside an HTML block
            'Variables' => ['api_content' => 'I am the email content!', 'api_title' => 'I am a title!'],
        ]
    ]
];
$response = $mj->post(Resources::$Email, ['body' => $body]);
$response->success() && var_dump($response->getData());
