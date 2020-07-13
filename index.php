<?php

require __DIR__ . '/vendor/autoload.php';

use Jumbojett\OpenIDConnectClient;

# Load the openid config
# these environment variables will be loaded from openid_config.env 
# if using the docker instructions in README.md
$openid_issuer = $_ENV['ISSUER'];
$openid_clientid = $_ENV['CLIENTID'];
$openid_secret = $_ENV['SECRET'];
$openid_redirect_url = $_ENV['REDIRECTURL'];

# The Jumbojett library will automatically call
# https://{$openid_issuer}/.well-known/openid-configuration
# to retrieve all the endpoints and info required
$oidc = new OpenIDConnectClient(
    $openid_issuer,
    $openid_clientid,
    $openid_secret
);

# The request is sent with the standard openid scopes
$oidc->addScope('openid email profile');

$oidc->addAuthParam(array('response_mode' => 'form_post'));
$oidc->setRedirectURL($openid_redirect_url);
$oidc->authenticate();

# Get the claims from the ID Token
$idtoken = $oidc->getVerifiedClaims();
$pretty_idtoken_json = json_encode($idtoken, JSON_PRETTY_PRINT);

# Call the OpenID userinfo endpoint
# Part of the OpenID Spec
# https://openid.net/specs/openid-connect-core-1_0.html#UserInfo
# 
# When using code flow Okta returns a "thin token" which is missing some claims
# such as given_name and family_name
# https://support.okta.com/help/s/article/Okta-Groups-or-Attribute-Missing-from-Id-Token
# We therefore need to call the Open ID userinfo endpoint with our access token
# (an access token is also provided separately to the id token)
$userinfo = $oidc->requestUserInfo();
$pretty_userinfo_json = json_encode($userinfo, JSON_PRETTY_PRINT);


# This is just a basic example
# At this point you would probably want to use the data to create a PHP session
# 
# This example does not implement sign-out i.e. $oidc->signOut($accessToken, $redirect)
# this method calls the sign-out URL (end_session_endpoint) which is provided as
# part of the openid metadata
# In production you should do this along with un-setting a session
#
# As it's just an example the next part just dumps the data to the browser

?>

<html>
<head>
    <title>Okta OpenID Connect Example</title>
    <style>
        body {
            font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
        }
        .boxed {
            border: 2px solid green;
            padding: 25px 25px 25px 25px;
        }
    </style>
</head>
<body>
    <h1>OpenID Connect Okta PHP Example</h1>
    <a href="https://openid.net/specs/openid-connect-core-1_0.html#StandardClaims">OpenID Standard Claims</a>
    <h2>Decoded ID Token</h2>
    <div class="boxed">
        <?php echo $pretty_idtoken_json; ?>
    </div>
    <h2>Raw userinfo response</h2>
    <div class="boxed">
        <?php echo $pretty_userinfo_json; ?>
    </div>
    <h2>Example attributes for the application</h2>
    <div>
        <table>
            <tr>
                <td>preferred_username:</td>
                <td><?php echo $userinfo->preferred_username; ?></td>
            </tr>
            <tr>
                <td>given_name:</td>
                <td><?php echo $userinfo->given_name; ?></td>
            </tr>
            <tr>
                <td>family_name:</td>
                <td><?php echo $userinfo->family_name; ?></td>
            </tr>
            <tr>
                <td>name:</td>
                <td><?php echo $userinfo->name; ?></td>
            </tr>
            <tr>
                <td>email:</td>
                <td><?php echo $userinfo->email; ?></td>
            </tr>
            <tr>
                <td>alternate_login:</td>
                <td><?php echo $userinfo->alternate_login; ?></td>
            </tr>
        </table>
        * alternate_login is a custom attribute and is not part of the OpenID spec
 
    </div>

</body>
</html>