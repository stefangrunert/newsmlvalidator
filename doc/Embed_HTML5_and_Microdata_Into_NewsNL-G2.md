#Embedding HTML5 polyglot + schema.org microdata into NewsML-G2

## Summary
HTML5 polyglott (aka XHTML5)  extended with schemas for structured data markup in microdata syntax is a powerful data structure for delivering news content in a standardized way. Since HTML5 polyglot documents are valid XML it’s parsable with Xpath and easily to embed them into NewsML-G2 contentSet/inlineXML elements. Both HTML5 polyglot and structured can be validated using validation services. An open question is how a document in the inlineXML element can be identified to be HTML5 polyglot. That is needed for distinct it from XHTML 1.x. An extension of the NewsML-G2 specification might be required.

## Definitions

### HTML5 polyglot
"A document that uses [polyglot markup](http://dev.w3.org/html5/html-polyglot/#dfn-polyglot-markup) is a document that is a stream of bytes that parses into identical document trees ... when processed either as HTML or when processed as XML. Polyglot markup that meets a well-defined set of constraints is interpreted as compatible, regardless of whether it is processed as HTML or as XHTML, per the HTML5 specification. Polyglot markup uses a specific DOCTYPE, namespace declarations, and a specific case—normally lower case but occasionally camel case—for element and attribute names. Polyglot markup uses lower case for certain attribute values. Further constraints include those on void elements, named entity references, and the use of scripts and style.“

Source: [Polyglot Markup: A robust profile of the HTML5 vocabulary](http://dev.w3.org/html5/html-polyglot/)

HTML5 polyglot is basically serialized HTML5 as XML and is a subset of valid HTML5. It’s identified by the HTTP header "Content-type: application/xhtml+xml", the doctype declaration “&lt;! DOCTYPE html?&gt;“ and the HTML namespace attribute “http://www.w3.org/1999/xhtml”. There are several other namespaces for elements not covered by the HTML specification, see http://www.w3.org/TR/2011/WD-html5-20110405/namespaces.html

### schema.org Vocabulary and Microdata

* schema.org is an extensible vocabulary for describing the semantics of elements i HTML documents.

* Microdata is a lightweight syntax for embedding semantic information in HTML documents

## Pro and Contra for Using HTML5 polyglot + schema.org Microdata with NewsML-G2

### HTML5 polyglot

+ +HTML5 is one of the most common data standards, and and can be serialized to be XML compliant 

+ +Has a couple of elements relevant for News (article, aside, figure, video…)

+ +Can be extended with microdata

+ +Validators exist

+ +"All" developers master HTML

+ +The document can be used as-it-is for previews et al

- -The HTML5 polyglot specification is currently only a candidate recommendation

- -There is no DTD/XSD to validate HTML5. Services has to be used.     
[http://www.w3.org/TR/html5/the-xhtml-syntax.html#writing-xhtml-documents]

- -There is currently no standardized way to identify NewsML-G2 embedded HTML5 polyglot as such

### schema.org microdata

+ +IPTC has contributed to the schemas with rNews, so it’s a quite natural choice

+ +The provided schemas cover basically the scope of NITF

+ +Can be embedded in HTML5

+ +Self-explaining, lightweight structure

+ +Widespread used

+ +Extensible
  
[https://schema.org/docs/extension.html]

+ Validators exist

+ Backed by Google, Bing, Yandex, Yahoo!

+ Is perfect for delivering SEO optimized news to our customers

+ No proprietary, non-standardized markup needed to tag article content semantically correct

## Alternatives

* XHTML 1.1
    * outdated

* NITF
    * covers exclusively the purpose of news exchange
    * unknown for the majority of developers

## Resources

### HTML5

Specification

[W3C HTML Polyglot Candidate Recommendation](http://www.w3.org/TR/html-polyglot/)

Articles

[Benefits of polyglot XHTML](http://www.xmlplease.com/xhtml/xhtml5polyglot/)

Validators

[validator.nu](https://validator.nu/)

[W3C Markup Validation Service](http://validator.w3.org/)

### Microdata

Specification

[schema.org](http://schema.org/)

[IPTC rNews](http://dev.iptc.org/rNews)

Validators

[Google Structured Data Testing Tool](https://developers.google.com/structured-data/testing-tool/)

[Bing Markup Validator](http://www.bing.com/toolbox/markup-validator)

[Yandex Structured Data Validator](https://webmaster.yandex.com/microtest.xml)

[Structured Data Linter](http://linter.structured-data.org/)

## Examples
````
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
       <meta charset="UTF-8"/>
   <title>Bacon ipsum dolor ...</title>
    </head>
    <body>
   <article itemscope="itemscope" itemtype="http://schema.org/NewsArticle">
       <h1 itemprop="headline">Bacon ipsum...
       </h1>
       <div itemprop="description">
           <p>Short loin...
           </p>
       </div>
       <div itemprop="associatedMedia">
           <figure itemscope="itemscope" itemtype="http://schema.org/ImageObject">
               <img itemprop="image" src="Forelle.jpg"   width="841" height="581"/>
               <figcaption>
                   <div itemprop="headline">Europäische Forelle</div>
                   <div itemprop="description">
                       <p>Die Bachforelle...</p>
                   </div>
                   <div itemprop="author">Don Alfonso</div>
               </figcaption>
           </figure>
       </div>
       <div itemprop="articleBody">
           <p>Meatloaf ham..
           </p>
           <figure itemscope="itemscope" itemtype="http://schema.org/ImageObject">
               <img itemprop="image" src="Flunder.jpg" width="398" height="240"/>
               <figcaption>
                   <div itemprop="headline">Flunder</div>
                   <div itemprop="description">
                       <p>Sie ist flach, flach, flach...</p>
                   </div>
                   <div itemprop="author">Don Fernando</div>
               </figcaption>
           </figure>
           <p>Chuck corned beef.</p>
       </div>
       <div itemscope="itemscope" itemtype="http://www.w3.org/2004/02/skos/corefoobar">
           <span itemprop="definition">Explanation...</span>
       </div>
   </article>
   </body>
</html>
````
