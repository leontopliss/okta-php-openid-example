# Okta PHP OpenID Connect example

An example using jumbojett OpenID connect libaries (https://github.com/jumbojett/OpenID-Connect-PHP) with Okta

The example shows how to authenticate a user and get additional user profile infomation using
the userinfo endpoint

As the example is a server side (non public) client it uses the authorization code flow (https://developer.okta.com/docs/concepts/auth-overview/#choosing-an-oauth-20-flow)


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