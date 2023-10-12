# Introduction
Within the description of the TETTRIs Task 3.2 "Automatic mapping of taxonomic expertise", it is stated that for the various expert groups gender balance should be one of the factors to profile for. Since the analysis on the various groups should be done automatically, it is necessary to estimate the gender balance of a group without manual curation. One approach that we are considering is to do this estimate based on the given names of the identified experts. This repository lays the ground work for such an approach.

In this repository we have gathered the 15000 most prominent given names from Wikidata and calculated their prevalent gender based of the genders assigned to the people in Wikidata that have those names. 

This is clearly a heuristical approach. **The data from this repository is not to be used to assess the gender of any individual**, but only to determine the gender balance amongst a group of people with room for statistical errors . 

We are aware that this approach relies on many oversimplifications as well as biases in the underlying data and will address some of them in this document. 

This document assumes a general understanding of the core concepts and general modelling approaches on Wikidata.


## Oversimplifications

### Oversimplification of Gender

There are [over 40 items](https://www.wikidata.org/wiki/Property:P21#P2302) that are can be assign as a "sex or gender" (P21) to a human in Wikidata.. For this analysis however we only focus on six of those genders that we combined into two simplified groups: 'male' ([Q6581097](http://www.wikidata.org/entity/Q6581097)), 'cisgender male' ([Q15145778](http://www.wikidata.org/entity/Q15145778))  and  'trans-man' ([Q2449503](http://www.wikidata.org/entity/Q2449503)) were grouped as 'male' and 'female' ([Q6581072](http://www.wikidata.org/entity/Q6581072)), 'cisgender female' ([Q15145779](http://www.wikidata.org/entity/Q15145779))  and  'trans-woman' ([Q1052281](http://www.wikidata.org/entity/Q1052281)) were grouped as 'female' respectively.
Out of the over 80 million name usages recorded in this dataset, only about 1700 were from people who had a "sex or gender" property with a value that was not one of those six. So for the simplicity of processing, these alternative genders were grouped as "other" during the processing. They were taken into consideration when asserting the confidence of the prevalent gender of a name. 

### Oversimplification of Names
The way names are structured is highly dependent on the cultural context. For this analysis we assume that a person has one or more given names. While it might be the case for many European/western cultures, we deliberately avoid calling them "first names, as this is not always the case. Wikidata calls the corresponding property "given name", so we decided to go with this designation as well. 
See also the humorous list of [Falsehoods programmers believe about names](https://www.kalzumeus.com/2010/06/17/falsehoods-programmers-believe-about-names/).

## Biases in underlaying data. 
### Gender Bias in Wikidata
Wikidata in general has a gender bias towards men, the reasons for which are manifold and beyond the scope of this analysis. Other publications state the overall percentage of women is around 20-25% of all of the persons in Wikidata, with that number getting closer to 50% for people born within the last 50 years. This leaning is reflected on the number of names gathered in this analysis. Out of the top 15000 names within Wikidata, around 10200 were predominantly male and around 4800 predominantly female. The total number of name usages shows an even larger disparity with 7,2 million to 1,9 million. To our surprise the average number of names per person per gender is both really low and not very different, with 1,0032 for male names and 1,0042 for female names. Though we were only able to analyze this for a random subset of 400,000 before the Wikidata Query Service run into a timeout [[Query 1](#q1)].

### Historic Bias
Other biases that also feed into the analysis are historic biases, e.g. names that were historically used for one particular gender but that have become more unisex in their usage in the last decades might be misrepresented. 

### Euro-centric Bias
Also the people and names recorded in Wikidata are biased towards traditionally European or western cultures. But since the goal of the analysis is to judge the gender balance amongst groups of European taxonomic experts, we decided that this is not an issue for our purposes. However people who want to use this dataset in a different context, need to be aware of this fact and potentially adjust their methodology accordingly. 

# Modelling of Given Names in Wikidata


In Wikidata the usual way that given names for a person are expressed is by using the [Property P735 (given name)](http://www.wikidata.org/entity/P735) for each given name of a person. The property links to an item for that given name. This item is an instance of [Q202444 (given name)](http://www.wikidata.org/entity/Q202444) or one of its subclasses such as [Q11879590 (female given name)](http://www.wikidata.org/entity/Q11879590) or [Q12308941 (male given name)](http://www.wikidata.org/entity/Q12308941). In total there are 144 subclasses of [Q202444 (given name)](http://www.wikidata.org/entity/Q202444) [[Query 2](#q2)]. A name item then usually has the [Property P1705 (native label)](http://www.wikidata.org/entity/P1705) to describe the name in different languages or scripts. This property is of the data type ‘monolinugal text’ which means that it also has a language attribute associated with it. In many cases of western names in Latin script the language attribute is set to "mul" for multilingual. A name can have multiple native labels, which sometimes are expressing the same name string for different languages. For names from languages with non-Latin scripts it is common to have transliterations expressed as native labels as well. The same native label can occur on multiple names.

Wikidata has around 105000 items that are either an instance of given name or an instance of its subclasses [[Query 3](#q3)]. These names have around 84000 different native labels (name label plus language) [[Query 4](#q4)]. Also there are around 10.6 million persons described in Wikidata [[Query 5](#q5)] but not all of them have their given names modelled by using the Property P:735 (given name). However trying to determine how many of those persons have properly modelled given names [[Query 6](#q6)] caused the Wikidata Query Service to run into a query time out.


## Unisex names
There is no consistent way how unisex names are expressed in Wikidata. Sometimes there is one item for a name that is an instance of [Q3409032 (unisex given name)](http://www.wikidata.org/entity/Q3409032), other times there are two items for one name, one for the male and one for the female usage of the name. In other rare cases, there are even 3 items, one describing the name as a unisex name and then one for the female and the male usage of the name that are subclasses of the unisex name. But even in cases where the different gender versions of the name, they are usually not consistently assigned to the people bearing them. 

## Modelling inconsistences
While the inconsistency in the modelling is particular noteworthy for the unisex names, they also exist for all of the other modelling aspects described above. For example, many persons don’t have their given names modelled or some given names contain a native label with the language tag "mul" and other native labels with the same name string for specific languages, which would already be covered under the "multiple" language variant. With our approach we focus on the common practices and try to capture the knowledge expressed in those ways using the queries for the analysis, while being aware that this might cause some inconsistently modelled names not to be captured properly. 


# Processing Steps
## 1 Export Given Names from Wikidata 


At first, we ran a query that combined the different native labels for given names and counted the number of people that have a name that is represented by one of those labels. The results were then ordered by their number of occurrences and thus gave us a list of the most common given names in Wikidata. 

We choose to limit this list to 15000 when we exported it, as this would put the cut off count at around 20 occurrences per name, which is a good minimum to be able determine the prevalent gender of a name with a sufficient confidence. The query we ran was [[Query 7](#q7)] and its output is saved as the file ```given_names_by_prevalence_in_WD_15000.wikidata.tsv``` in this repository,.

The result of the query was then imported into OpenRefine for the next processing steps.

## 2 Querying the gender balance for each name

For each name we then ran a query to Wikidata to determine the genders of the people using it. The query itself can be seen in [[Query 8](#q8)], here for the name "George"@mul, which would of course be adjusted for each row in the dataset. 

At this point we would have liked to run the query just with name string without the language tag. And while this would have technically been possible in SPARQL, even the simple tests with rarely used names ran into query timeouts, which is why we continued to use the name with the language tag. Also this query counts how many times the name itself is used, which means that name usages of names that have multiple native values are also counted multiple times. 

Processing the results of the query would have been too complex to be done in OpenRefine with its General Refine Expression Language (GREL). So instead of querying the WDSQ directly, OpenRefine send the request to a simple, locally run PHP script that would then construct the query for the WDSQ, send it and process the results before returning them to OpenRefine. For each name it would return 4 numbers, separated by a pipe symbol "|": the number of usages of that name by male persons, the number of usages by female persons, the number of usages by persons with a gender other then male or female and the number of usages by persons with no expressed gender. The PHP script is the file ```given_names.php``` in this repository. Querying all of the rows of the OpenRefine project individually took several hours of processing, we put a throttle delay between each query so we wouldn’t send too many queries against the WDQS within too short of a time. 

The results from the queries were then process further by splitting them into individual columns, converting them to numbers, renaming and reordering them.

## 3 Combining identical name string with different language tags
To understand the motivation for the next processing step, lets look at the example of the name "Gabriele". In Italian it is a traditionally male name, whereas in Germany it is a traditionally female name. Wikidata has 3 items that cover this name with different native labels. 


| Item  | Description  | Native Label  | Count Male | Count Female |
|---|---|---|---|---|
| [Q104804603](https://www.wikidata.org/wiki/Q104804603)  | unisex given name  | "Gabriele"@mul  | 6 | 26
| [Q17765515](https://www.wikidata.org/wiki/Q17765515)  | male given name  | "Gabriele"@mul  | 676 | 27
| [Q17765518](https://www.wikidata.org/wiki/Q17765518)  | female given name  | "Gabriele"@de  | 7 | 448


In the previous processing steps these names are combined to

| Native Label  | Count Male | Count Female |
|---|---|---|
|  "Gabriele"@mul  | 682 | 53
|  "Gabriele"@de  | 7 | 448

If we were to calculate the prevalent gender and confidence scores as outlined in the next section to these results, it would give us a 93% confidence that "Gabriele"@mul is a male name and a 98% confidence that "Gabriele"@de is a female name. However since our primary use-case describes that we get a first name, without a context of the language, we would need to query just for "Gabriele", which would return two different results, both with very high levels of confidence. 

To counter this, a new column "nameLabel_count" was introduced that shows how often each name string occurs independet of the language tag [[Query 9](#q9)] and afterwards four queries were run (one for each count column) to combine the counts of the names with the same name string, but different name tags. This was done by selecting the largest number for each column from the different names that match. [[Query 10](#q10)] In total there are 184 names that occur more than once (176 twice, 8 thrice) to which this processing was applied. 

This step gives us the following result: 


| Name Label  | Count Male | Count Female |
|---|---|---|
|  "Gabriele"  | 682 | 448

And thus resulting in a confidence score that indicates that no heuristical analysis should be done with this name.

The reason not to just sum up the counts for each column was that it would favor incorrectly modelled modeled names, that have the same name string applied with different name tags, such as in the case of "Jean"



**Data in Wikidata**

[Jean (Q7521081)](https://www.wikidata.org/wiki/Q7521081): male given name 

| Native Label  | Count Male | Count Female | Count Other |
|---|---|---|---|
|  "Jean"@mul <br />"Jean"@fr| 41950 | 54 | 6

[Jean (Q4160311)](https://www.wikidata.org/wiki/Q4160311):  female given name

| Native Label  | Count Male | Count Female | Count Other |
|---|---|---|---|
|  "Jean"@mul| 172 | 3269 | 2


**Data from the processing step 2:**


| Native Label  | Count Male | Count Female | Count Other |
|---|---|---|---|
|  "Jean"@mul| 42122 | 3323 | 8
|  "Jean"@fr| 41950 | 54 | 6	

Combining the numbers by summing them up, would count the numbers for the male given name twice. 

| Name Label  | Count Male | Count Female | Count Other |
|---|---|---|---|
|  "Jean"| 84072 | 3377 | 14

But now we get a more representative count for 

| Name Label  | Count Male | Count Female | Count Other |
|---|---|---|---|
|  "Jean"| 42122 | 3323 | 8

In this particular example the numbers are the same as just the ones for "Jean"@mul, but in other more complex cases, the numbers would be a combination of the most relevant counts of the name usages. While this is not as good as the data we would get from properly modelled items, but since we needed to automatically process 15000 names, it is sufficient for our purposes.

As the results all of the name strings with the different language tags now have the same occurrence counts, thus technically reducing the number of names in the dataset to 14801. When using this dataset to check for a given name, the user can always use the first match of the name string, knowing that all other will return the same result.

## 4 Calculating Prevalent Gender and Confidence
As the next step we determined the prevalent gender for each name. However, due to the gender bias in Wikidata, we added a normalization factor, to account for the overrepresentation of male names. A new column was added and the normalization factor was determined for the dataset. The value is 3.2851 and is the same for the entire dataset, but to ease the next processing steps, this is still set for each row individually. [[Query 11](#q11)]
If the number of usages for males was larger than the number of usages for females times the normalization factor plus the number of usages for other genders, the prevalent gender would be male, and the other way around [[Query 12](#q12)]. Only in 3 cases the result was inconclusive because the number of usages were so close together. But the assertion of the prevalent gender is not helpful without a score of the confidence. The calculation for such a score was the count for the prevalent gender divided by the sum of the counts for the prevalent gender, the non-prevalent gender and the other genders. The counts for the female name occurrences were again multiplied by the normalization factor, regardless of where in the calculation they would appear. 


```
confidence = (count for previlant_gender) / ( (count for previlant_gender) + (count for non_previlant_gender) + (count for other_gender) )
```

[[Query 13](#q13)]
# Using the results 
Using the heuristical approach to determine the gender of a person should always be the last option, if analysis from other sources have failed to give a conclusive result. For our target use-case the default step would be to check if the person has an item on Wikidata and if said item has a statement about their gender.
To determine the gender balance of a specific group of people each person first needs to be checked for an individual source of their gender. If none was found, then one needs to look up the string of the given name of each researcher in the column ‘nameLabel’ in the table given-names-by-prevalence-in-WD-15000.openrefine.tsv and check the prevalent gender (column "prevalent_gender") and the confidence score (column "confidence"). If there are multiple names that match the label, they all will return the same results, so just using the first match is fine. Our recommendation is to only use it for names with a confidence of >95%. Name with a lower confidence score should not be omitted, but counted as "undetermined" to accurately represent uncertainty and atypical name usages. Once the results of all of the researchers have been tallied, a combined statement can be made, such as: 60% male, 30% female and 10% undetermined. We recommend a minimum group size of 10 people to account for uncertainties with a particular caution in cases of groups where the results for all of the people are of the same gender. 

# Files in this repository
- given_names.php
    - The php-script for querying the gender balance for each name from processing step 2
- given_names_by_prevalence_in_WD_15000.wikidata.tsv 
    - the result of the wikidata query from processing step 1
- given_names_results.txt
    - the log of the queries run by given_names.php
- given-names-by-prevalence-in-WD-15000.openrefine.tar.gz
    - the OpenRefine project archive file
- given-names-by-prevalence-in-WD-15000.openrefine.tsv
    - the resulting table after the processing, exported from OpenRefine as tab separated values (tsv)
- given-names-by-prevalence-in-WD-15000.openrefine-history.json
    - the exported processing steps from OpenRefine
- LICENSE
    - the license file for this repository: Mozilla Public License Version 2.0
- README.md
    - this file


# Other remarks
* In total around 8.2 million name occurrences were analyzed for this data set, see [[Query 14](#q14g)].
* The precise numbers used in this text and in the analysis in OpenRefine might differ from each other and from the results from live query due to the ever evolving nature of Wikidata. The analysis described in this report was done in July 2023 and the example numbers shown here reflect that point in time as well.

# Queries
## 1: Average number of given names by gender<span id="q1"></span>
```
SELECT ?gender ?genderLabel ?average ?sum ?count{
  {
  SELECT ?gender (SUM(?nameCount) as ?sum) (COUNT(?person) as ?count) (SUM(?nameCount)/COUNT(?person) as ?average) WHERE {
    SELECT ?person ?gender (COUNT(?name) as ?nameCount) WHERE {
      SELECT ?person ?gender ?name WHERE {
        ?person wdt:P31 wd:Q5.
        ?person wdt:P21 ?gender.
        ?person wdt:P735 ?name
      } LIMIT 400000
    } GROUP BY ?person ?gender
  } GROUP BY ?gender
  }
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
} ORDER BY DESC(?sum)
LIMIT 7
```
[run query](https://query.wikidata.org/#SELECT%20%3Fgender%20%3FgenderLabel%20%3Faverage%20%3Fsum%20%3Fcount%7B%0A%20%20%7B%0A%20%20SELECT%20%3Fgender%20%28SUM%28%3FnameCount%29%20as%20%3Fsum%29%20%28COUNT%28%3Fperson%29%20as%20%3Fcount%29%20%28SUM%28%3FnameCount%29%2FCOUNT%28%3Fperson%29%20as%20%3Faverage%29%20WHERE%20%7B%0A%20%20%20%20SELECT%20%3Fperson%20%3Fgender%20%28COUNT%28%3Fname%29%20as%20%3FnameCount%29%20WHERE%20%7B%0A%20%20%20%20%20%20SELECT%20%3Fperson%20%3Fgender%20%3Fname%20WHERE%20%7B%0A%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP31%20wd%3AQ5.%0A%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20%3Fgender.%0A%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP735%20%3Fname%0A%20%20%20%20%20%20%7D%20LIMIT%20400000%0A%20%20%20%20%7D%20GROUP%20BY%20%3Fperson%20%3Fgender%0A%20%20%7D%20GROUP%20BY%20%3Fgender%0A%20%20%7D%0A%20%20SERVICE%20wikibase%3Alabel%20%7B%20bd%3AserviceParam%20wikibase%3Alanguage%20%22%5BAUTO_LANGUAGE%5D%2Cen%22.%20%7D%0A%7D%20ORDER%20BY%20DESC%28%3Fsum%29%0ALIMIT%207)

Result (excerpt): 

|gender                                 |genderLabel|average               |sum   |count |
|---------------------------------------|-----------|----------------------|------|------|
|http://www.wikidata.org/entity/Q6581097|male       |1.00326384925196755444|249598|248786|
|http://www.wikidata.org/entity/Q6581072|female     |1.00441862641549794778|150255|149594|


## 2: Number of names that are direct instances of the item 'given name' each of its subclasses<span id="q2"></span>
```
SELECT ?nameClass ?nameClassLabel ?count WHERE {
  {
    SELECT ?nameClass (COUNT(?name) as ?count){
      ?name wdt:P31/wdt:P279* wd:Q202444.
      ?name wdt:P31 ?nameClass.
    }GROUP BY ?nameClass
  }
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
} ORDER BY DESC(?count)
```
[run query](https://query.wikidata.org/#SELECT%20%3FnameClass%20%3FnameClassLabel%20%3Fcount%20WHERE%20%7B%0A%20%20%7B%0A%20%20%20%20SELECT%20%3FnameClass%20%28COUNT%28%3Fname%29%20as%20%3Fcount%29%7B%0A%20%20%20%20%20%20%3Fname%20wdt%3AP31%2Fwdt%3AP279%2a%20wd%3AQ202444.%0A%20%20%20%20%20%20%3Fname%20wdt%3AP31%20%3FnameClass.%0A%20%20%20%20%7DGROUP%20BY%20%3FnameClass%0A%20%20%7D%0A%20%20SERVICE%20wikibase%3Alabel%20%7B%20bd%3AserviceParam%20wikibase%3Alanguage%20%22%5BAUTO_LANGUAGE%5D%2Cen%22.%20%7D%0A%7D%20ORDER%20BY%20DESC%28%3Fcount%29)

Result (excerpt): 

|nameClass                               |nameClassLabel    |count|
|----------------------------------------|------------------|-----|
|http://www.wikidata.org/entity/Q12308941|male given name   |50037|
|http://www.wikidata.org/entity/Q11879590|female given name |30641|
|http://www.wikidata.org/entity/Q202444  |given name        |25919|
|http://www.wikidata.org/entity/Q1243157 |double name       |5191 |
|http://www.wikidata.org/entity/Q3409032 |unisex given name |3121 |
|http://www.wikidata.org/entity/Q22809413|Chinese given name|2816 |


## 3: Number of given names in Wikidata <span id="q3"></span>
```
SELECT (COUNT(?name) as ?count) WHERE {
  ?name wdt:P31/wdt:P279* wd:Q202444.
}
```
[run query](https://query.wikidata.org/#SELECT%20%28COUNT%28%3Fname%29%20as%20%3Fcount%29%20WHERE%20%7B%0A%20%20%20%20%3Fname%20wdt%3AP31%2Fwdt%3AP279%2a%20wd%3AQ202444.%0A%7D)

Result: 108843

## 4: Number of different nameLabel and language combinations <span id="q4"></span>
```
SELECT (COUNT(?nameString) as ?count) WHERE {
  {
    SELECT DISTINCT ?nameString{
      ?name wdt:P31/wdt:P279* wd:Q202444.
      ?name wdt:P1705 ?nameString.
    }
  }
}
```
[run query](https://query.wikidata.org/#SELECT%20%28COUNT%28%3FnameString%29%20as%20%3Fcount%29%20WHERE%20%7B%0A%20%20%7B%0A%20%20%20%20SELECT%20DISTINCT%20%3FnameString%7B%0A%20%20%20%20%20%20%3Fname%20wdt%3AP31%2Fwdt%3AP279%2a%20wd%3AQ202444.%0A%20%20%20%20%20%20%3Fname%20wdt%3AP1705%20%3FnameString.%0A%20%20%20%20%7D%0A%20%20%7D%0A%7D)

Result: 86755

## 5: Number of persons in Wikidata <span id="q5"></span>
```
SELECT (COUNT(?person) as ?count) WHERE {
  ?person wdt:P31 wd:Q5.
}
```
[run query](https://query.wikidata.org/#SELECT%20%28COUNT%28%3Fperson%29%20as%20%3Fcount%29%20WHERE%20%7B%0A%20%20%20%20%20%20%3Fperson%20wdt%3AP31%20wd%3AQ5.%0A%7D)

Result: 10779963

## 6: Number of persons in Wikidata without modelled given names <span id="q6"></span>
```
SELECT (COUNT(?person) as ?count) WHERE {
  ?person wdt:P31 wd:Q5.
  FILTER NOT EXISTS { ?person wdt:P735 [] }
}
```
[run query](https://query.wikidata.org/#SELECT%20%28COUNT%28%3Fperson%29%20as%20%3Fcount%29%20WHERE%20%7B%0A%20%20%3Fperson%20wdt%3AP31%20wd%3AQ5.%0A%20%20FILTER%20NOT%20EXISTS%20%7B%20%3Fperson%20wdt%3AP735%20%5B%5D%20%7D%0A%7D)

Result: Time out

## 7: Top 15000 given names in Wikidata with their number of usages <span id="q7"></span>
```
SELECT ?nameLabel ?nameLang (SUM(?countName) as ?count) WHERE {
  {
    SELECT ?name (COUNT(?person) as ?countName) WHERE {
      ?name wdt:P31/wdt:P279* wd:Q202444.
      ?person wdt:P735 ?name.
    }GROUP BY ?name
    ORDER BY DESC(?countName)
    #LIMIT 100000
  }
  ?name wdt:P1705 ?nameLabel.
  BIND(LANG(?nameLabel) as ?nameLang).
}GROUP BY ?nameLabel ?nameLang
ORDER BY DESC(?count) ASC(?nameLabel)
LIMIT 15000
```
[run query](https://query.wikidata.org/#SELECT%20%3FnameLabel%20%3FnameLang%20%28SUM%28%3FcountName%29%20as%20%3Fcount%29%20WHERE%20%7B%0A%20%20%7B%0A%20%20%20%20SELECT%20%3Fname%20%28COUNT%28%3Fperson%29%20as%20%3FcountName%29%20WHERE%20%7B%0A%20%20%20%20%20%20%3Fname%20wdt%3AP31%2Fwdt%3AP279%2a%20wd%3AQ202444.%0A%20%20%20%20%20%20%3Fperson%20wdt%3AP735%20%3Fname.%0A%20%20%20%20%7DGROUP%20BY%20%3Fname%0A%20%20%20%20ORDER%20BY%20DESC%28%3FcountName%29%0A%20%20%20%20%23LIMIT%20100000%0A%20%20%7D%0A%20%20%3Fname%20wdt%3AP1705%20%3FnameLabel.%0A%20%20BIND%28LANG%28%3FnameLabel%29%20as%20%3FnameLang%29.%0A%7DGROUP%20BY%20%3FnameLabel%20%3FnameLang%0AORDER%20BY%20DESC%28%3Fcount%29%20ASC%28%3FnameLabel%29%0ALIMIT%2015000%0A%20%20)

Result (first 6 rows): 

|nameLabel|nameLang|count |
|---------|--------|------|
|John     |mul     |136653|
|William  |mul     |85704 |
|Paul     |mul     |83096 |
|Robert   |mul     |69709 |
|Thomas   |mul     |64285 |
|James    |en      |60497 |


## 8: Gender distribution per name <span id="q8"></span>
```
SELECT ?name ?nameLabel ?gender ?count WHERE {
  {
    SELECT ?name ?gender (COUNT(?person) as ?count) WHERE {
      BIND("George"@mul as ?nameString).
      ?name wdt:P31/wdt:P279* wd:Q202444.
      ?name wdt:P1705 ?nameString.
      ?person wdt:P735 ?name.
      {
        {
          #male
          ?person wdt:P21 wd:Q6581097.
        }UNION{
          #cisgender male
          ?person wdt:P21 wd:Q15145778.
        }UNION{
          #trans man
          ?person wdt:P21 wd:Q2449503.
        }
        BIND("male" as ?gender).
      }UNION{
        {
          #female
          ?person wdt:P21 wd:Q6581072.
        }UNION{
          #cisgender female
          ?person wdt:P21 wd:Q15145779.
        }UNION{
          #trans woman
          ?person wdt:P21 wd:Q1052281.
        }
        BIND("female" as ?gender).
      }UNION{
        ?person wdt:P21 [].
        MINUS{
          ?person wdt:P21 wd:Q6581097.
        }
        MINUS{
          ?person wdt:P21 wd:Q15145778.
        }
        MINUS{
          ?person wdt:P21 wd:Q2449503.
        }
        MINUS{
          ?person wdt:P21 wd:Q6581072.
        }
        MINUS{
          ?person wdt:P21 wd:Q15145779.
        }
        MINUS{
          ?person wdt:P21 wd:Q1052281.
        }
        BIND("other" as ?gender).
      }UNION{
        FILTER NOT EXISTS {?person wdt:P21 [].}
        BIND("unspecified" as ?gender).
      }
    }GROUP BY ?name ?gender
  }
  
  SERVICE wikibase:label { bd:serviceParam wikibase:language "en,en". }
}ORDER BY DESC(?count)
```
[run query](https://query.wikidata.org/#SELECT%20%3Fname%20%3FnameLabel%20%3Fgender%20%3Fcount%20WHERE%20%7B%0A%20%20%7B%0A%20%20%20%20SELECT%20%3Fname%20%3Fgender%20%28COUNT%28%3Fperson%29%20as%20%3Fcount%29%20WHERE%20%7B%0A%20%20%20%20%20%20BIND%28%22George%22%40mul%20as%20%3FnameString%29.%0A%20%20%20%20%20%20%3Fname%20wdt%3AP31%2Fwdt%3AP279%2a%20wd%3AQ202444.%0A%20%20%20%20%20%20%3Fname%20wdt%3AP1705%20%3FnameString.%0A%20%20%20%20%20%20%3Fperson%20wdt%3AP735%20%3Fname.%0A%20%20%20%20%20%20%7B%0A%20%20%20%20%20%20%20%20%7B%0A%20%20%20%20%20%20%20%20%20%20%23male%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ6581097.%0A%20%20%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%20%20%23cisgender%20male%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ15145778.%0A%20%20%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%20%20%23trans%20man%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ2449503.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20BIND%28%22male%22%20as%20%3Fgender%29.%0A%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%7B%0A%20%20%20%20%20%20%20%20%20%20%23female%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ6581072.%0A%20%20%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%20%20%23cisgender%20female%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ15145779.%0A%20%20%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%20%20%23trans%20woman%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ1052281.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20BIND%28%22female%22%20as%20%3Fgender%29.%0A%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20%5B%5D.%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ6581097.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ15145778.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ2449503.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ6581072.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ15145779.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20MINUS%7B%0A%20%20%20%20%20%20%20%20%20%20%3Fperson%20wdt%3AP21%20wd%3AQ1052281.%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20BIND%28%22other%22%20as%20%3Fgender%29.%0A%20%20%20%20%20%20%7DUNION%7B%0A%20%20%20%20%20%20%20%20FILTER%20NOT%20EXISTS%20%7B%3Fperson%20wdt%3AP21%20%5B%5D.%7D%0A%20%20%20%20%20%20%20%20BIND%28%22unspecified%22%20as%20%3Fgender%29.%0A%20%20%20%20%20%20%7D%0A%20%20%20%20%7DGROUP%20BY%20%3Fname%20%3Fgender%0A%20%20%7D%0A%20%20%0A%20%20SERVICE%20wikibase%3Alabel%20%7B%20bd%3AserviceParam%20wikibase%3Alanguage%20%22en%2Cen%22.%20%7D%0A%7DORDER%20BY%20DESC%28%3Fcount%29)

Result (for the example "George"@mul from above): 

|name                                    |nameLabel|gender     |count|
|----------------------------------------|---------|-----------|-----|
|http://www.wikidata.org/entity/Q15921732|George   |male       |44885|
|http://www.wikidata.org/entity/Q15921732|George   |unspecified|229  |
|http://www.wikidata.org/entity/Q15921732|George   |female     |40   |
|http://www.wikidata.org/entity/Q23774368|George   |female     |15   |
|http://www.wikidata.org/entity/Q23774368|George   |male       |4    |
|http://www.wikidata.org/entity/Q15921732|George   |other      |3    |

## 9: Determine the number of occurrences for each label <span id="q9"></span>
* create a new column "nameLabel_count" based on column "count_undefined" with the expression
```
length(cells["nameLabel"].cross("","nameLabel").cells["count"].value)
```
Result: For most names this is one, but there are 374 rows with a value of 2 (thus 187 names that occur twice) and 18 rows with a value of 3 (6 names that occur thrice)

## 10: Combining identical name string with different language tags <span id="q10"></span>
* for the column "count_male" apply a text transformation with the expression
```
cells["nameLabel"].cross("","nameLabel").cells["count_male"].value.sort().reverse()[0]
```
* for the column "count_female" apply a text transformation with the expression
```
cells["nameLabel"].cross("","nameLabel").cells["count_female"].value.sort().reverse()[0]
```
* for the column "count_other" apply a text transformation with the expression
```
cells["nameLabel"].cross("","nameLabel").cells["count_other"].value.sort().reverse()[0]
```
* for the column "count_undefined" apply a text transformation with the expression
```
cells["nameLabel"].cross("","nameLabel").cells["count_undefined"].value.sort().reverse()[0]
```

## 11: Determine the normalization factor <span id="q11"></span>
* create a new column "dummy" that has the same fixed value for each row, e.g. "x".
* for the column "dummy" select "Add column based on this column ..."
* enter the following code as the expression
```
round(sum(cell.cross("","dummy").cells["count_male"].value)*1.0/sum(cell.cross("","dummy").cells["count_female"].value)*10000)/10000.0
```

Result: 3.2851

## 12: Determine the prevalent gender <span id="q12"></span>
* create a new column "prevalent_gender" based on column "count_undefined" with the expression
```
if(cells["count_male"].value > cells["count_female"].value*cells["normalization_factor"].value + cells["count_other"].value, 'male', if(cells["count_female"].value *cells["normalization_factor"].value > cells["count_male"].value + cells["count_other"].value, 'female', 'inconclusive'))
```



## 13: Calculate the confidence score <span id="q13"></span>
* create a text facet for the column "prevalent_gender" and select the value for "male"
* create a new column "confidence" based on column "prevalent_gender" with the expression
```
round(cells["count_male"].value*1.0/(cells["count_male"].value + cells["count_female"].value*cells["normalization_factor"].value + cells["count_other"].value)*100000)/100000.0
```
* in the text facet for the column "prevalent_gender" and select the value for "female"
* apply a text transformation on the column "confidence" with the expression
```
round(cells["count_female"].value*cells["normalization_factor"].value/(cells["count_male"].value + cells["count_female"].value*cells["normalization_factor"].value + cells["count_other"].value)*100000)/100000.0
```

## 14: Calculate the sum of all of the name occurrences in the column count in OpenRefine <span id="q14"></span>
* use the dummy column created in [Query 11](#q11) and select "Add column based on this column ..."
* enter the following code as the expression
```
sum(cell.cross("","dummy").cells["count"].value))
```
* in the preview for any of the rows, the sum of all of the values is now shows
* the new column doesn't need to be created

Result: 8188705

