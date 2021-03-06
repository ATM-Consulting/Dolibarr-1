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

namespace   Splash\Local\Objects\Product;

/**
 * @abstract    Dolibarr Products Core Fields (Required) 
 */
trait CoreTrait {

    /**
    *   @abstract     Build Core Fields using FieldFactory
    */
    protected function buildCoreFields()   {

        global $conf, $langs;

        //====================================================================//
        // Reference
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("ref")
                ->Name($langs->trans("ProductRef"))
                ->IsListed()
                ->MicroData("http://schema.org/Product","model")
                ->isLogged()
                ->isRequired();

        //====================================================================//
        // Name
        $this->FieldsFactory()
                ->Create($conf->global->MAIN_MULTILANGS ? SPL_T_MVARCHAR : SPL_T_VARCHAR)
                ->Identifier("label")
                ->Name($langs->trans("ProductLabel") . ($conf->global->MAIN_MULTILANGS ? ' (M)' : Null))
                ->IsListed()
                ->isLogged()
                ->Group($langs->trans("Description"))
                ->MicroData("http://schema.org/Product","name")
                ->isRequired();

    }    


    /**
     *  @abstract     Read requested Field
     * 
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     * 
     *  @return         none
     */
    protected function getCoreFields($Key,$FieldName)
    {
        //====================================================================//
        // READ Fields
        switch ($FieldName)
        {
            //====================================================================//
            // Direct Readings
            case 'ref':
                $this->getSimple($FieldName);
                break;
            
            case 'label':
                $this->getMultilang($FieldName);
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
    protected function setCoreFields($FieldName,$Data) 
    {

        //====================================================================//
        // WRITE Field
        switch ($FieldName)
        {
            //====================================================================//
            // Direct Writtings
            case 'ref':
                $this->setSimple($FieldName, $Data);
                break;       
            
            case 'label':
                $this->setMultilang($FieldName, $Data);
                //====================================================================//        
                // Duplicate Lable to Deprecated libelle variable
                $this->Object->libelle = $this->Object->label;
                break;
                
            default:
                return;
        }
        unset($this->In[$FieldName]);
    }

    
}
