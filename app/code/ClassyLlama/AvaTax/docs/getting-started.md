<!-- This list is in each of the documentation files. Ensure any updates are applied to the list in each file. -->
# Documentation

- [Home](../README.md)
- [Getting Started](./getting-started.md)
- Extension Features
  - [Sales Tax](./sales-tax.md)
  - [Address Validation](./address-validation.md)
  - [Customs, Duty & Import Tax (CDIT)](./customs-duty-import-tax.md) (available in the [2.x.x Release Candidate version of this extension](./getting-started.md#version-notes))
  - [Document Management (Tax Exemptions)](./document-management.md) (available in the [2.x.x Release Candidate version of this extension](./getting-started.md#version-notes))

# Getting Started

## Table of Contents

- [Getting Started](#getting-started)
  * [Support](#support)
  * [Prerequisites](#prerequisites)
  * [Supported Magento Versions](#supported-magento-versions)
  * [Installation](#installation)
    + [Version Notes](#version-notes)
    + [Install via Composer](#install-via-composer)
    + [Install by Copying Files](#install-by-copying-files)
  * [Configuration](#configuration)
  * [Important Notes](#important-notes)
- [Common Problems](#common-problems)
  * [Soap Client Not Found](#soap-client-not-found)
- [Known Issues](#known-issues)
- [Release Notes](#release-notes)
- [Pre-Launch Sales Record Cleanup](#pre-launch-sales-record-cleanup)
- [Uninstall Extension](#uninstall-extension)
- [Troubleshooting](#troubleshooting)

## Getting Started

### Support

For support with your AvaTax account, please visit [avalara.com/technical-support](http://www.avalara.com/Technical-Support). This software will not work unless you have a valid AvaTax account. To obtain the required account information, please contact your Avalara representative.

### Prerequisites

- Active AvaTax account with a company setup, including applicable nexuses
- Magento running on a server that has the following:
  - Properly configured CRON job
  - The OpenSSL and SoapClient PHP extensions (required by the AvaTax library)

### Supported Magento Versions

Refer to [README](https://github.com/classyllama/ClassyLlama_AvaTax#magento-version-support) for supported versions/editions.

###  Installation

>  Please note, that installing the 2.x.x releases of AvaTax will remove all your config settings for any 1.x.x version you currently have installed. Please go through the configuration steps again if this is not a fresh install of AvaTax.

#### Version Notes

There are two versions of this extension:

* 2.x.x Release Candidate - This version contains the following enhancements to this extension: [Customs, Duty & Import Tax](./customs-duty-import-tax.md) and [Document Management](./document-management.md). If you'd like to use this version of the extension, you will need to work with your Avalara support representative to ensure your account has these features activated. Please report issues by [creating Github issues](https://github.com/classyllama/ClassyLlama_AvaTax/issues).
* 1.x.x Stable - This version supports Sales Tax & Address Validation. If you don't need any of the new features present in 2.x.x, you should install this version.

#### Install via Composer

This is the recommended installation method as it allows you to easily update the extension in the future. **Important:** Installation must be performed by an experienced Magento developer and these instructions assume that is the case. Installation support can only be provided to developers.

1. Require the desired version of AvaTax (see [note above](#version-notes) for differences in the versions):

   1. 2.x.x Release Candidate: 

      The 2.x.x Release Candidate currently requires Classy Llama's fork of the AvaTax PHP Library. To ensure you get the correct version of the Library, add the following to your project's composer.json repositories:
      ```
      "classyllama-avatax": {
        "type": "git",
        "url": "git@github.com:classyllama/AvaTax-REST-V2-PHP-SDK.git"
      }
      ```
      
      Also, add the following to the same composer.json `require` section:
      ```
      "avalara/avataxclient": "dev-integration/release-2.0.0 as 18.4.3.191"
      ```

      Finally, require the ClassyLlama_AvaTax module:

      ```
      composer require classyllama/module-avatax:2.0.0-RC1
      ```

   2. 1.x.x Stable

      ```
      composer require classyllama/module-avatax
      ```

2. Setup the AvaTax module in magento

   ```bash
   bin/magento module:enable --clear-static-content ClassyLlama_AvaTax
   bin/magento setup:upgrade
   bin/magento cache:flush
   ```

3. If you are deploying the extension to a production environment, follow the [devdocs.magento.com deployment instructions](http://devdocs.magento.com/guides/v2.0/howdoi/deploy/deploy-to-prod.html#deploy-prod)

#### Install by Copying Files

Only use this installation method if you're unable to use Composer. The installation method above is the preferred method.

**Important:** Installation must be performed by an experienced Magento developer and these instructions assume that is the case. Installation support can only be provided to developers.

1. Create an `app/code/ClassyLlama/AvaTax` directory in your Magento installation.

2. Download the latest source code from this page: [https://github.com/classyllama/ClassyLlama_AvaTax/releases](https://github.com/classyllama/ClassyLlama_AvaTax/releases )

3. Extract the file and copy the contents of the ClassyLlama_AvaTax-XXX directory into the `app/code/ClassyLlama/AvaTax` directory.

4. Make sure the [Avalara AvaTax PHP library](https://github.com/avadev/AvaTax-Calc-SOAP-PHP/tree/v15.5.1.0) is installed as the module depends on it.

   ```bash
   composer require avalara/avatax:15.5.2.0
   ```

5. To confirm the Avalara AvaTax library exists check for the presence of `vendor/avalara/avatax` in your Magento installation directory. NOTE: You **should** use Composer to download the avalara/avatax dependency because files in the `vendor` directory only get loaded by PHP if the dependencies were installed via Composer. If for whatever reason you're unable to use Composer to install extensions, you can review this [Github issue](https://github.com/classyllama/ClassyLlama_AvaTax/issues/62) for a workaround.

6. Run following commands from your root Magento installation directory

   ```bash
   bin/magento module:enable --clear-static-content ClassyLlama_AvaTax
   bin/magento setup:upgrade
   bin/magento cache:flush
   ```

7. If you are deploying the extension to a production environment, follow the [devdocs.magento.com deployment instructions](http://devdocs.magento.com/guides/v2.0/howdoi/deploy/deploy-to-prod.html#deploy-prod)

### Configuration

1. To configure the extension, go to `Stores > Settings > Configuration > Sales > Tax.` 
2. Details on configuring each of the extension features:
  - [Sales Tax](./sales-tax.md#configuration)
  - [Address Validation](./address-validation.md#configuration)
  - [Customs, Duty & Import Tax (CDIT)](./customs-duty-import-tax.md#configuration)
  - [Document Management (Tax Exemptions)](./document-management.md#configuration)	
3. In the Magento admin, go to `Stores > Settings > Configuration > Sales > Shipping Settings`. Click on the **Origin** section. Enter the address that will be used as the origin or **Shipped From** location in AvaTax's tax calculation. **It is *required*** that you specify a postal code in the origin address, no matter what country is specified. Otherwise you will get errors in checkout and when saving customers.

### Important Notes

- Magento discounts are subtracted from the price before the total amount is sent to AvaTax to retrieve tax information. AvaTax is not sent any information about the discount amount, as the AvaTax 15 API does not support itemized discounting the way Magento does.
- The extension has been tested in multiple currencies.
- Transaction IDs in AvaTax are mapped to Magento's invoice and credit memo numbers.
- Default tax reporting in Magento displays tax collected by each tax rule you created in the system. However, when using AvaTax, Magento tax rules are not used; instead, tax rules and calculations are configured within AvaTax. Because of this, Magento's tax report will (correctly) show that there are no Magento tax rules collecting tax. A full tax report suite is available within your AvaTax dashboard.
- If you've customized checkout at all, it is your responsibility to confirm the continued functionality of Address Validation.
- AvaTax will only accept an invoice ID and credit memo ID once per store. Thus if you're testing in environments that are capable of duplicating those values, be careful to increment those values in Magento or create new test stores in AvaTax.
- Every time you save a customer in Magento, Magento will reach out and attempt to update that customer's data in AvaTax. If the customer doesn't exist, the module just ignores the error and will try again the next time you save the customer in Magento. This happens on customer save within the admin, as well as on the front end.

## Common Problems

### Soap Client Not Found

After installing extension, you get an error like this:

```
PHP Fatal error: Class 'SoapClient' not found in /vendor/avalara/avatax/AvaTax/DynamicSoapClient.php on line 21
```

Solution: You'll need to install the PHP SoapClient library, per the [prerequisites](#prerequisites) section.

## Known Issues

- Gift Wrapping and Multi-Address Checkout - If a customer checks out using the multi address checkout, uses multiple ship-to addresses, and adds gift wrapping to any of the items, once the customer places the order, the **totals** section for each of the resulting orders will contain the gift wrapping price, regardless of whether that order contains gift wrapping.
- <a name="admin-order-create-sales-tax-issue">Admin Guest Order Create Sales Tax Issue With Customer Group</a> - If you are creating an order in the admin for a new user and select a customer group that should be tax exempt please be aware that the totals section on the create order form may not always calculate tax correctly. Placing the order will collect the tax totals as expected.
- Unit of Measure HS Codes are currently in development within AvaTax's API, therefore the API around Unit of Measure is unstable. For this reason, this module currently does not support any HS Codes that require a Unit of Measure. Avoid using HS Codes with Unit of Measure as they will break checkout.
- If a user proceeds to the 2nd step of checkout (chooses a shipping address and shipping method), then returns to the cart, the cart tax estimator will send 2 requests to the AvaTax API for tax information. It is also possible during these 2 calculations, that no shipping information will be provided, therefore the estimator can show that there is no cost for shipping. If you proceed to checkout, on the 2nd step of checkout the tax calculations, including shipping, will be accurate.

## Release Notes

See this page for release notes: [https://github.com/classyllama/ClassyLlama_AvaTax/blob/master/CHANGELOG.md](https://github.com/classyllama/ClassyLlama_AvaTax/blob/master/CHANGELOG.md)


## Pre-Launch Sales Record Cleanup

If you have installed the AvaTax extension in a development/testing environment and are planning on deleting orders, invoices, credit memos, etc before launching your site (for example, [see these queries](http://stackoverflow.com/a/39415254/2726785)), when you delete those records, you'll also want to delete all records from the following tables. This is important in order to avoid errors when records are inserted into the `avatax_queue` tables.

```sql
-- Truncate AvaTax tables
TRUNCATE TABLE `avatax_queue`;
TRUNCATE TABLE `avatax_log`;
```

## Uninstall Extension

1. Run this command in the root of your Magento installation directory: `bin/magento module:uninstall ClassyLlama_AvaTax`

2. If you installed the module using Composer, run these commands in the root of your Magento installation directory:

   ```bash
   composer remove classyllama/module-avatax
   composer remove avalara/avatax
   ```

   If you installed the module by copying files, run these commands in the root of your Magento installation directory:

   ```bash
   rm -rf app/code/ClassyLlama/Avatax
   composer remove avalara/avatax
   ```

3. Run the following queries in your Magento database:

   ```bash
   -- Remove AvaTax tables (these tables will be in the sales database in split-database mode)
   DROP TABLE `avatax_queue`;
   DROP TABLE `avatax_log`;
   DROP TABLE `avatax_sales_creditmemo`;
   DROP TABLE `avatax_sales_invoice`;
   
   -- Remove column from Tax Class table
   ALTER TABLE `tax_class` DROP COLUMN `avatax_code`;
   
   -- Remove columns from Creditmemo table (this is only relevant for versions of the extension older than 0.4.0)
   ALTER TABLE `sales_creditmemo` DROP COLUMN `avatax_is_unbalanced`;
   ALTER TABLE `sales_creditmemo` DROP COLUMN `base_avatax_tax_amount`;
   
   -- Remove columns from Invoice table (this is only relevant for versions of the extension older than 0.4.0)
   ALTER TABLE `sales_invoice` DROP COLUMN `avatax_is_unbalanced`;
   ALTER TABLE `sales_invoice` DROP COLUMN `base_avatax_tax_amount`;
   ```



## Troubleshooting

Before contacting support, performing the following steps to increase the extension’s logging level and review the logged details of an API request may reveal what the underlying issue is; and if not, may still provide helpful information for troubleshooting: 

**Increase AvaTax’s Database logging level**

- In the backend, navigate to `Stores > Configuration > Sales > Tax > AvaTax Settings`
- Scroll down to the **Logging Settings** section and change the following settings:
  - **Database Log Level** = Debug
  - **Database Log Detail** = Extra 
    ![](images/Configuration__Settings__Stores__Magento_Admin_2017-09-03_20-45-22.png?raw=true)

Reproduce the issue that is being experienced on the site and then retrieve the API request’s details: 

**Retrieve the API details for the AvaTax call(s)**

- In the backend, navigate to `Stores > AvaTax Logs`
- Locate the log entry for the SOAP call that matches the test case and click the **View** link 
  ![](images/AvaTax_Logs__Taxes__Stores__Magento_Admin_2017-09-03_20-48-18.png?raw=true)

Reviewing the details of the **Request** and **Result** sections can reveal errors that may not be presented on the frontend.

- If you are getting no tax results, check for an error such as an expired Avalara account.

- If the tax results don’t match what is expected, check the address values being sent in the request. 
  ![](images/2017-08-01_130753__Taxes__Stores__Magento_Admin_2017-09-03_20-52-04.png_1423792_2017-09-03_20-58-22.png?raw=true)

- Also check the item(s) being sent in the request

  ![](images/2017-08-01_130753__Taxes__Stores__Magento_Admin_2017-09-03_21-01-04.png?raw=true)

_Note: If this is a production environment, be sure to change the logging settings back to the recommended values to prevent excessive logging._ 

When requesting support for the extension, including the **Request** and **Result** details in the support request may result in a quicker resolution.
