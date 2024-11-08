<?php
/* WARNING: The contents of this file is cached by Swarm. Changes to
 * it will not be picked up until the cached versions are removed.
 * See the documentation on the 'Swarm config cache'.
 */
return array(
    'environment' => array(
        'hostname' => 'localhost',
        # external_urlを設定した場合、P4ServerのSwarmアクセス値URLは上書きされる。
        'external_url' => 'https://access-hostname/',
    ),
    'p4' => array(
        'port' => 'ssl:helixcore_address:1666',
        'user' => 'swarm',
        'password' => '${after_inject}',
        'sso' => 'enabled',
    ),
    // this block should be a peer of 'p4'
    'security' => array(
        // Helix Authentication Serviceのno-sso設定に所属するユーザーはSwarmAPIにパスワード認証で入れてしまう。
        // SwarmAPIへのアクセスを禁止する目的でこちらに記述してSwarm自体にログインできなくする
        'prevent_login' => array(
            'super',
            'jenkins',
            'swarm',

        ),
    ),
    // SSO設定・ここから
    // the saml block should be a peer of 'p4' located at the end of 
    // the Swarm configuration block in the config.php file
    'saml' => array(
        // If your Helix Server trigger expects a message header so that it can 
        // easily recognize SAML response messages, add the header text
        'header' => 'saml-response: ', // leave empty for no message header ''
        // Service Provider Data that we are deploying
        'sp' => array(
            // Identifier of the SP entity  (must be a URI)
            // 何でもいいが、helix-authentication-service側と一致させる必要あり
            'entityId' => 'urn:xxx-swarm-sso:sp',
            // Specifies info about where and how the AuthnResponse message MUST be
            // returned to the requester, in this case our SP.
            'assertionConsumerService' => array(
                // URL Location where the Response from the IdP will be returned, this is the Swarm URL and port
                'url' => 'http(or,s)://swarmsite_address',
            ),
        ),
        // Identity Provider Data that we want to connect to with our SP (Swarm)
        'idp' => array(
            // Identifier of the IdP entity  (must be a URI)
            'entityId' => 'urn:auth-svc:idp',
            // SSO endpoint info of the IdP. (Authentication Request protocol)
            'singleSignOnService' => array(
                // URL Target of the IdP where the SP (Swarm) will send the Authentication Request Message
                'url' => 'https://helix-authentication-service_url:port/saml/login',
            ),

            // The x509cert of the idp is provided by the following x509cert parameter.
            // Do not add the privateKey parameter. 
            // You must use the x509cert parameter, you must not add the cert file the certs folder. 
            // helix-authentication-service側で利用しているSSL証明書本文(上部下部のBEGIN…を抜いた本文のみ)が必要
            'x509cert' => 'XXXYYZZZ',
        ),
    ),
    // SSO設定・ここまで
    // Swarmワークフロー "On update of a review in an end state" を有効化するために必要
    // this block should be a peer of 'p4'
    'reviews' => array(
        'end_states' => array('archived', 'rejected', 'approved:commit'),
    ),
    'mail' => array(
        // 'sender' => 'swarm@my.domain',   // defaults to 'notifications@hostname'
        'transport' => array(
            'name' => 'localhost',          // name of SMTP host
            //利用するメールサーバーの指定
            'host' => 'smtp.gmail.com',          // host/IP of SMTP host
            'port' => 587,                  // SMTP host listening port
            'connection_class'  => 'plain', // 'smtp', 'plain', 'login', 'crammd5'
            'connection_config' => array(   // include when auth required to send
                'username'  => 'noreply-XXX@gamestudio.co.jp',      // user on SMTP host
                'password'  => 'XXXXX',      // password for user on SMTP host
                'ssl'       => 'tls',       // empty, 'tls', or 'ssl'
            ),
            // override email deliveries and store all messages in this path
            // 'path' => '/var/spool/swarm',
        ),

        // override regular recipients; send email only to these addresses
        // 'recipients' => array(
        //     'user1@my.domain',
        //     'user2@my.domain',
        // ),

        // send notifications of comments to comment authors?
        'notify_self' => false,

        // blind carbon-copy recipients
        // 'use_bcc' => true,

        // suppress reply-to header
        // 'use_replyto' => false,

        // change the email subject prefix, the default prefix is '[Swarm]'
        // 'subject_prefix' => '[Swarm]',

        // Control email thread indexing
        // 'index-conversations' => true, // ['true'|'false'] default is true, email thread indexing is turned on
    ),
);
