@acceptance-back
Feature: Enrich a table attribute value
  In order to enrich my products according to my business rules
  As a product manager
  I need to be able to see validation errors when enriching a table

  Background:
    Given an authenticated user
    And the following attributes:
      | code        | type                     | table_configuration                                                                                                                                                                                                                                                                                                                                                                                                      |
      | sku         | pim_catalog_identifier   |                                                                                                                                                                                                                                                                                                                                                                                                                          |
      | nutrition   | pim_catalog_table        | [{"id": "ingredient_f6492fb4-d815-4d30-a912-8db321a3e38a", "code": "ingredient", "data_type": "select"}, {"id": "quantity_f967d82a-b54c-41da-959e-1fa43124afee", "code": "quantity", "data_type": "number", "validations": {"min": 10, "max": 100, "decimals_allowed": false}}, {"id": "description_8bb00280-d04f-4c19-a6cf-46b83ad9553d", "code":"description", "data_type":"text", "validations": {"max_length": 15}}] |
    And the following select options:
      | attribute_code | column_code | options                               |
      | nutrition      | ingredient  | [{"code": "sugar"}, {"code": "salt"}] |
    And the following locales "en_US"
    And the following "ecommerce" channel with locales "en_US"

  Scenario: Providing a table with valid data should not raise any violation
    When a product is created with values:
      | attribute   | json_data                                                                    |
      | nutrition   | [{"ingredient": "sugar", "quantity": 100, "description": "the description"}] |
    Then no product violation is raised

  Scenario: Providing a table with number lower than 10 raises an violation
    When a product is created with values:
      | attribute   | json_data                                                                  |
      | nutrition   | [{"ingredient": "sugar", "quantity": 5, "description": "the description"}] |
    Then 1 violation is raised
    And the violation 'This value should be 10 or more.' is raised at path 'values[nutrition-<all_channels>-<all_locales>][0].quantity'

  Scenario: Providing a table with number greater than 100 raises an violation
    When a product is created with values:
      | attribute   | json_data                                                                    |
      | nutrition   | [{"ingredient": "sugar", "quantity": 101, "description": "the description"}] |
    Then 1 violation is raised
    And the violation 'This value should be 100 or less.' is raised at path 'values[nutrition-<all_channels>-<all_locales>][0].quantity'

  Scenario: Providing a table with number with decimal raises an violation
    When a product is created with values:
      | attribute   | json_data                                                                                                                   |
      | nutrition   | [{"ingredient": "sugar", "quantity": 50.5, "description": "the description"}, {"ingredient": "salt", "quantity": "30.14"}] |
    Then 2 violations are raised
    And the violation 'The required value is an integer' is raised at path 'values[nutrition-<all_channels>-<all_locales>][0].quantity'
    And the violation 'The required value is an integer' is raised at path 'values[nutrition-<all_channels>-<all_locales>][1].quantity'

  Scenario: Providing a table with text longer than 15 raises an violation
    When a product is created with values:
      | attribute   | json_data                                                                             |
      | nutrition   | [{"ingredient": "sugar", "quantity": 10, "description": "the very long description"}] |
    Then 1 violation is raised
    And the violation 'This value should contain 15 characters or less.' is raised at path 'values[nutrition-<all_channels>-<all_locales>][0].description'
