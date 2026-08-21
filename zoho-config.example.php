<?php
declare(strict_types=1);

return [
    'enabled' => true,

    // Use the domain for your Zoho account: zoho.com, zoho.in, zoho.eu, zoho.com.au, etc.
    'accounts_domain' => 'https://accounts.zoho.com',
    'api_domain' => 'https://www.zohoapis.com',

    'client_id' => 'YOUR_ZOHO_CLIENT_ID',
    'client_secret' => 'YOUR_ZOHO_CLIENT_SECRET',
    'refresh_token' => 'YOUR_ZOHO_REFRESH_TOKEN',

    'module' => 'Leads',
    'lead_source' => 'ASD Market Week Landing Page',
    'duplicate_check_fields' => ['User_ID'],
    'user_id_source' => 'email',

    // Replace custom field API names after checking Zoho CRM > Setup > Developer Space > APIs > API Names.
    'field_map' => [
        'user_id' => 'User_ID',
        'first_name' => 'First_Name',
        'last_name' => 'Last_Name',
        'company' => 'Company',
        'phone' => 'Phone',
        'email' => 'Email',
        'lead_source' => 'Lead_Source',
        'service' => 'Type_of_Service',
        'message' => 'Description',
    ],
];
