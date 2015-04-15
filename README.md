# Validator for NewsML-G2 + (( HTML 5 polyglot + Microdata ) || XHTML1.0 ) + NITF 

            
## How it works
The validation is performed in four independent steps:
1) NewsML-G2 validation based on the XSD provided by IPTC.
2) HTML validation of the inlineXML embedded HTML document withing the NewsML-G2 contentSet,
using the API of https://validator.nu
3) Validation of microdata, embedded in the HTML document, (ab)using  http://linter.structured-data.org
4) NITF is validated using XSD schemas

## Validation API
Validation without using the graphical interface can be done by sending a POST request,
containing the NewsML-G2 document in the POST body to the same URL as this page.

## Alternative validation services
You can choose between a couple of services to validate HTML5 and Microdata. 
Please remember to add the right doctype definition to your XHTML document in order 
to make the validator recognize this is a polyglot HTML5 document (<!DOCTYPE html>)

###HTML5 validation
http://validator.w3.org/, The W3C Markup Validation Service
https://validator.nu, validator.nu

###Microdata validation
https://developers.google.com/structured-data/testing-tool, Google Testing Tool,
https://webmaster.yandex.com/microtest.xml, Yandex Structured Data Validator
http://www.bing.com/toolbox/markup-validator, Bing Markup Validator
    
##Want to Contribute?
Feel free to checkout the project, improve it and send me a pull request.
    
##Installation
Dependencies: HTTP server running PHP

