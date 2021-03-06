<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 * 
 **/

namespace Splash\Local\Objects\ThirdParty;

use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    Dolibarr ThirdParty Address Fields 
 */
trait AddressTrait {

        
    /**
     *  @abstract     Build Address Fields using FieldFactory
     */
    protected function buildAddressFields() {
        global $langs;
        
        $GroupName = $langs->trans("CompanyAddress");
        //====================================================================//
        // Addess
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("address")
                ->Name($langs->trans("CompanyAddress"))
                ->Group($GroupName)
                ->isLogged()
                ->MicroData("http://schema.org/PostalAddress","streetAddress");

        //====================================================================//
        // Zip Code
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("zip")
                ->Name( $langs->trans("CompanyZip"))
                ->Group($GroupName)
                ->MicroData("http://schema.org/PostalAddress","postalCode")
                ->isLogged()
                ->isListed();
        
        //====================================================================//
        // City Name
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("town")
                ->Name($langs->trans("CompanyTown"))
                ->Group($GroupName)
                ->isLogged()
                ->MicroData("http://schema.org/PostalAddress","addressLocality");
        
        //====================================================================//
        // Country Name
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("country")
                ->Name($langs->trans("CompanyCountry"))
                ->Group($GroupName)
                ->ReadOnly()
                ->isListed();
        
        //====================================================================//
        // Country ISO Code
        $this->FieldsFactory()->Create(SPL_T_COUNTRY)
                ->Identifier("country_code")
                ->Name($langs->trans("CountryCode"))
                ->Group($GroupName)
                ->isLogged()
                ->MicroData("http://schema.org/PostalAddress","addressCountry");
        
        //====================================================================//
        // State Name
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("state")
                ->Group($GroupName)
                ->Name($langs->trans("State"))
                ->ReadOnly();
        
        //====================================================================//
        // State code
        $this->FieldsFactory()->Create(SPL_T_STATE)
                ->Identifier("state_code")
                ->Name($langs->trans("State Code"))
                ->Group($GroupName)
                ->MicroData("http://schema.org/PostalAddress","addressRegion")
                ->NotTested();
        
    }
    
    /**
     *  @abstract     Read requested Field
     * 
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     * 
     *  @return         none
     */
    protected function getAddressFields($Key,$FieldName)
    {
        //====================================================================//
        // READ Fields
        switch ($FieldName)
        {
            //====================================================================//
            // Direct Readings
            case 'address':
            case 'zip':
            case 'town':
            case 'state':
            case 'state_code':
            case 'country':
            case 'country_code':
                $this->getSimple($FieldName);
                break;
            default:
                return;
        }
        
        unset($this->In[$Key]);
    }
    
    /**
     *  @abstract     Write Given Fields
     * 
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     * 
     *  @return         none
     */
    protected function setAddressFields($FieldName,$Data) 
    {
        //====================================================================//
        // WRITE Field
        switch ($FieldName)
        {
            case 'address':
            case 'zip':
            case 'town':
                $this->setSimple($FieldName, $Data);
                break; 
            
            case 'country_code':
                $this->setSimple("country_id", $this->getCountryByCode($Data));
                break;                    
            
            case 'state_code':
                //====================================================================//
                // If Country Also Changed => Update First
                if ( isset( $this->In["country_code"] )) {
                    $this->setSimple("country_id", $this->getCountryByCode($this->In["country_code"]));
                }
                $StateId    =   $this->getStateByCode($Data, $this->Object->country_id);
                $this->setSimple("state_id",$StateId);
                break;  
            
            default:
                return;
        }
        unset($this->In[$FieldName]);
    }
    
}
