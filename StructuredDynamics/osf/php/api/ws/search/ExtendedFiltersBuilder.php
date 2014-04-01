<?php

  /*! @ingroup OSFPHPAPIWebServices OSF PHP API Web Services */
  //@{

  /*! @file \StructuredDynamics\osf\php\api\ws\search\ExtendedFiltersBuilder.php
      @brief ExtendedFiltersBuilder class description
   */               
               
  namespace StructuredDynamics\osf\php\api\ws\search;
  

  /**
    * Class used to generate a set of extended attribute filters that should be added
    * to a SearchQuery. These extended attributes filters support grouping of 
    * attributes/values filters along with the boolean operators AND, OR and NOT.
    * 
    * Here is an example of how this API should be used to create an extended
    * search filters for the SearchQuery class:
    *      
    * @code
    * 
    *   $search = new SearchQuery($network);
    * $extendedFiltersBuilder = new ExtendedFiltersBuilder();
    * 
    * $results = $search->mime("resultset")
    *                   ->extendedFilters(
    *                       $extendedFiltersBuilder->startGrouping()
    *                                                  ->attributeValueFilter("http://purl.org/ontology/iron#prefLabel", "cancer AND NOT (breast OR ovarian)")
    *                                              ->endGrouping()
    *                                              ->and_()
    *                                              ->startGrouping()
    *                                                  ->attributeValueFilter("http://purl.org/ontology/nhccn#useGroupSignificant", "http://purl.org/ontology/doha#liver_cancer", TRUE)
    *                                                  ->or_()
    *                                                  ->attributeValueFilter("http://purl.org/ontology/nhccn#useGroupSignificant", "cancer")
    *                                              ->endGrouping()
    *                                              ->and_()
    *                                              ->datasetFilter("file://localhost/data/ontologies/files/doha.owl")                                               
    *                                              ->getExtendedFilters())
    *                   ->send()
    *                   ->getResultset();
    * 
    * @endcode 
    * 
    * This code will produce this "extended_filters" parameter value:
    * 
    *  (http%253A%252F%252Fpurl.org%252Fontology%252Firon%2523prefLabel:cancer%2BAND%2BNOT%2B%2528breast%2BOR%2Bovarian%2529) 
    *  AND (http%253A%252F%252Fpurl.org%252Fontology%252Fnhccn%2523useGroupSignificant[uri]:http%255C%253A%252F%252Fpurl.org%252Fontology%252Fdoha%2523liver_cancer 
    *  OR http%253A%252F%252Fpurl.org%252Fontology%252Fnhccn%2523useGroupSignificant:cancer) AND 
    *  dataset:%22file%3A%2F%2Flocalhost%2Fdata%2Fontologies%2Ffiles%2Fdoha.owl%22
    *  
    * @see http://techwiki.openstructs.org/index.php/Search
    * 
    * @author Frederick Giasson, Structured Dynamics LLC.
    */
  class ExtendedFiltersBuilder
  {
    /**
    * Extended filters parameter string to use to feed $search->extendedFilters(...)
    */
    private $extendedFilters = "";
    
    function __construct(){}
    
    /**
    * Return the list of filters generated by the ExtendedFiltersBuilder class
    * used by the OSF Search endpoint for the "extended_filters" parameter.
    */
    function getExtendedFilters()
    {
      return($this->extendedAttributes);
    }

    /**
    * Add a dataset URI to filter
    *     
    * @param mixed $dataset Dataset URI to add to the extended filters query.
    * @return ExtendedFiltersBuilder
    */
    public function datasetFilter($dataset)
    {
      $this->extendedAttributes .= "dataset:".urlencode('"'.$dataset.'"');
      
      return($this);
    }
    
    /**
    * Add a type URI to filter
    * 
    * @param mixed $type Type URI to add to the extended filters query.
    * @param mixed $enableInference Enable inferencing for this type filter.
    * @return ExtendedFiltersBuilder
    */
    public function typeFilter($type, $enableInference = FALSE)
    {
      if($enableInference === FALSE)
      {
        $this->extendedAttributes .= "type:".urlencode('"'.$type.'"');
      }
      else
      {
        $this->extendedAttributes .= "(type:".urlencode('"'.$type.'"').urlencode(" OR ").
                                     "inferred_type:".urlencode('"'.$type.'"').")";
      }
      
      return($this);
    }
    
    /**
    * Add an attribute/value filter
    * 
    * @param mixed $attribute Attribute URI to add to the extended filters query.
    * @param mixed $value Value to filter by. By default, all values are used ("*")
    * @param boolean $valueIsUri Specify if the value (or set of values) for this attribute have to be considered
    *                            as URIs (this should be specified to TRUE if the attribute is an object property)
    * @return ExtendedFiltersBuilder
    */
    public function attributeValueFilter($attribute, $value="*", $valueIsUri = FALSE)
    {
      // Check if there are Search endpoint control characters in the query.
      // If there are, then we don't escape the values and we assume
      // they are properly escaped.
      //
      // EXCEPT if the value is a URI
      
      str_replace(array(' OR ', ' AND ', ' NOT ', '\\', '+', '-', '&', 
                               '|', '!', '(', ')', '{', '}', '[', ']', '^', 
                               '~', '*', '?', '"', ';', ' '), "", $value, $found);
      
      if($found > 0 && !$valueIsUri)
      {
        $this->extendedAttributes .= urlencode(urlencode($attribute)).($valueIsUri === TRUE ? "[uri]" : "").":".
                                     urlencode(urlencode($value));
      }
      else
      {
        $this->extendedAttributes .= urlencode(urlencode($attribute)).($valueIsUri === TRUE ? "[uri]" : "").":".
                                     urlencode(urlencode($this->escape($value)));  
      }
      
      return($this);
    }
    
    /**
    * Add a AND operator to the extended filters query
    */
    public function and_()
    {
      $this->extendedAttributes .= urlencode(" AND ");
      
      return($this);
    }

    /**
    * Add a OR operator to the extended filters query
    */
    public function or_()
    {
      $this->extendedAttributes .= urlencode(" OR ");
      
      return($this);
    }

    /**
    * Add a NOT operator to the extended filters query
    */
    public function not_()
    {
      $this->extendedAttributes .= urlencode(" NOT ");
      
      return($this);
    }

    /**
    * Start grouping a series of filters
    */
    public function startGrouping()
    {
      $this->extendedAttributes .= "(";
      
      return($this);
    }
    
    /**
    * End grouping a series of filters
    */
    public function endGrouping()
    {
      $this->extendedAttributes .= ")";
      
      return($this);
    }
    
    /**
    * Escape reserver values characters by the Search endpoint.
    * 
    * @param mixed $string Value to filter
    */
    private function escape($string)
    {
      $match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
      $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');
      $string = str_replace($match, $replace, $string);

      return $string;
    }          
  }  

//@}      
?>
