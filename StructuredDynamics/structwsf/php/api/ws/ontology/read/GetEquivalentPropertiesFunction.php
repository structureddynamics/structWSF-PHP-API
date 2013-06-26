<?php

  /*! @ingroup StructWSFPHPAPIWebServices structWSF PHP API Web Services */
  //@{

  /*! @file \StructuredDynamics\structwsf\php\api\ws\ontology\read\GetEquivalentPropertiesFunction.php
      @brief GetEquivalentPropertiesFunction class description
   */

  namespace StructuredDynamics\structwsf\php\api\ws\ontology\read;  
  
  /**
  * Get all the equivalent-properties that have been defined in an ontology. The requester 
  * can get a list of URIs or the full description of the equivalent-properties. 
  *       
  * @see http://techwiki.openstructs.org/index.php/Ontology_Read#getEquivalentProperties
  * 
  * @author Frederick Giasson, Structured Dynamics LLC.
  */
  class GetEquivalentPropertiesFunction extends \StructuredDynamics\structwsf\php\api\framework\OntologyFunctionCall
  {
    function __construct()
    {
      // Default values
      $this->getDatatypeProperties();
      $this->getPropertiesUris();
      $this->allEquivalentProperties();
    }
        
    /**
    * URI of the property for which the requester want its equivalent-properties. 
    * 
    * **Required**: This function must be called before sending the query 
    * 
    * @param mixed $uri URI of the property for which the requester want its equivalent-properties. 
    *       
    * @see http://techwiki.openstructs.org/index.php/Ontology_Read#getEquivalentProperties
    * 
    * @author Frederick Giasson, Structured Dynamics LLC.
    */
    public function uri($uri)
    {
      $this->params["uri"] = $uri;
    }
    
    /**
    * Get all the Datatype equivalent-properties of the ontology  
    * 
    * @see http://techwiki.openstructs.org/index.php/Ontology_Read#getEquivalentProperties
    * 
    * @author Frederick Giasson, Structured Dynamics LLC.
    */
    public function getDatatypeProperties()
    {
      $this->params["type"] = "dataproperty";
    }
    
    /**
    * Get all the Object equivalent-properties of the ontology 
    * 
    * @see http://techwiki.openstructs.org/index.php/Ontology_Read#getEquivalentProperties
    * 
    * @author Frederick Giasson, Structured Dynamics LLC.
    */
    public function getObjectProperties()
    {
      $this->params["type"] = "objectproperty";
    }    
    
    /**
    * Get a list of URIs that refers to the properties described in this ontology. 
    * 
    * @see http://techwiki.openstructs.org/index.php/Ontology_Read#v
    * 
    * @author Frederick Giasson, Structured Dynamics LLC.
    */
    public function getPropertiesUris()
    {
      $this->params["mode"] = "uris";
    }
    
    /**
    * Get the list of properties description for the classes described in this ontology.
    * 
    * @see http://techwiki.openstructs.org/index.php/Ontology_Read#getEquivalentProperties
    * 
    * @author Frederick Giasson, Structured Dynamics LLC.
    */
    public function getPropertiesDescriptions()
    {
      $this->params["mode"] = "descriptions";
    }  
                    
    /**
    * Only get the direct equivalent-properties of the target property. 
    * 
    * @see http://techwiki.openstructs.org/index.php/Ontology_Read#getEquivalentProperties
    * 
    * @author Frederick Giasson, Structured Dynamics LLC.
    */
    public function directEquivalentProperties()
    {
      $this->params["direct"] = "True";
    }    

    /**
    * Get all the equivalent-properties by inference (so, the equivalent-properties of 
    * the equivalent-properties recursively). 
    * 
    * @see http://techwiki.openstructs.org/index.php/Ontology_Read#getEquivalentProperties
    * 
    * @author Frederick Giasson, Structured Dynamics LLC.
    */
    public function allEquivalentProperties()
    {
      $this->params["direct"] = "False";
    }         
  }
  
//@}    
?>