# Okta PHP OpenID Connect example

A basic example using jumbojett OpenID connect libraries (https://github.com/jumbojett/OpenID-Connect-PHP) with Okta

The example shows how to authenticate a user and get additional user profile information using
the userinfo endpoint.

As the example is a server side (non public) client it uses the authorization code flow (https://developer.okta.com/docs/concepts/auth-overview/#choosing-an-oauth-20-flow)


## Okta Claims

If you are using the Okta OpenID provider the issuer in the configuration should simply be the URL of your tenant. For example http://acme.okta.com
With Authorisation Code flow Okta returns a "thin" ID token which has some claims missing
for example given_name and family_name. However an access token is also returned which can
be used to access the standard OpenID Connect userinfo endpoint

If you have the Okta API management module you are able to bake custom claims into the ID token by using issuer url on your authorization server for example https://acme.okta.com/oauth2/default. Whilst it's possible to bake any custom claims into the ID token the OpenID standard claims cannot be included (producing the error "The claim name must be unique within an authorization server."). For example given_name cannot be added (given_name1 could be added) however as you probably want to stick to the OpenID specification a call to the userinfo endpoint is necessary to retrieve this data.

If you use implicit mode and request an ID token only then a "fat" token is returned. This does contain all of the custom claims however as this is a server side implementation the example uses auth code flow with a call to the userinfo endpoint.

## Okta Setup

If you don't have an Okta tenant it's possible to create limited tenant for testing by visiting https://developer.okta.com/signup/

When logged into the admin dashboard:
* Applications -> Create New Application
* Platform = 'Web', Select 'OpenID Connect'
* Enter an application name
* Enter a redirect URL. To the docker example below work: http://localhost

This redirects you to the application, then:
* On the general tab: Only Authorization code should be selected (default)
* Copy the client ID and credentials, these will be required in the application config (docker example openid_config.env)
* On the assignments tab assign users

These steps will allow the docker example below to work

## To Run

Install docker (If you are testing on a windows PC or MacOSX then you can use Docker Desktop)

The config is passed as environment variables
Update the environment variable file with your Okta tenant, application client ID and secret
```
cp openid_config-sample.env openid_config.env
# then edit openid_config.env with your config 
```

To build the docker container run:
```
docker build -t phpopenid .
```

To run (making nginx accessible on port 80):
```
docker run -p 80:80 -it --env-file=openid_config.env phpopenid
```