#IdeaBiz PHP sample

##Configuration
* Make Config.json and lib/data.json writable
* Change config.json files properties based on your application


For getting refresh token, you have to use token api with use username for one time

## Use
Once configure config.json, you can include `IdeaBizAPIHandler.php` to your code. then call `sendAPICall` method 

For example

```
include 'IdeaBizAPIHandler.php';
$auth = new IdeaBizAPIHandler();
$out = $auth->sendAPICall($url,RequestMethod::POST,$body);
```

## Parameters
### URL
 complete URL of ideabiz api. Example for sms `https://ideabiz.lk/apicall/smsmessaging/v1/outbound/94777123456/requests`
### Method
 its HTTP method. you can use `RequestMethod` Enum for that. it contain. this accept be string also. like "POST", "GET"

```
RequestMethod::POST
RequestMethod::GET
RequestMethod::DELETE
RequestMethod::PUT

```

### Body
this is plain string that contain any payload. If you need send object, please `json_encode` it.

```
$out = $auth->sendAPICall($url,RequestMethod::POST,json_encode($obj));

```


## Response
Result returns as array. 

### Success

```
 $result['status'] 
 $result['statusCode'] 
 $result['time']
 $result['header']
 $result['body']

```

#### status 
this contain OK for success

#### Status Code
this contain http status code. eg : 200, 400 like that

#### Time
Time that took to complete request

#### Headers
HTTP headers that returns by server

#### Body
body is plain text. if you cant you cant `json_decode` it



### Error
this happen if connection fail or errors other than Authentication failures


```
 $result['status'] 
 $result['error'] 
```


#### status 
this contain ERROR for for Errors

#### error
this contain error description


### Exceptions
This return two type of exceptions if any authentication errors

its
```
AuthenticationException
ConnectionException
```


### Example code
Please refer `test.php`


