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

namespace Splash\Local\Objects\Order;

/**
 * @abstract    Dolibarr Customer Orders Fields (Required) 
 */
trait CoreTrait {

    /**
     *  @abstract     Build Core Fields using FieldFactory
     */
    protected function buildCoreFields()   {
        global $langs;
        
        //====================================================================//
        // Customer Object
        $this->FieldsFactory()->Create(self::Objects()->Encode( "ThirdParty" , SPL_T_ID))
                ->Identifier("socid")
                ->Name($langs->trans("Company"))
                ->MicroData("http://schema.org/Organization","ID")
                ->isRequired();  
        
        //====================================================================//
        // Order Date 
        $this->FieldsFactory()->Create(SPL_T_DATE)
                ->Identifier("date")
                ->Name($langs->trans("OrderDate"))
                ->MicroData("http://schema.org/Order","orderDate")
                ->isRequired()
                ->IsListed();
        
        //====================================================================//
        // Reference
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("ref")
                ->Name($langs->trans("RefOrder"))
                ->MicroData("http://schema.org/Order","name")       
                ->ReadOnly()
                ->IsListed();

        //====================================================================//
        // Customer Reference
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("ref_client")
                ->Name($langs->trans("RefCustomerOrder"))
                ->IsListed()
                ->MicroData("http://schema.org/Order","orderNumber");
        
        //====================================================================//
        // Internal Reference
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("ref_int")
                ->Name($langs->trans("InternalRef"))
                ->MicroData("http://schema.org/Order","description");
                
        //====================================================================//
        // External Reference
        $this->FieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("ref_ext")
                ->Name($langs->trans("RefExt"))
                ->IsListed()
                ->MicroData("http://schema.org/Order","alternateName");
        
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
            case 'ref_client':
            case 'ref_int':
            case 'ref_ext':
                $this->getSimple($FieldName);
                break;
            
            //====================================================================//
            // Contact ThirdParty Id 
            case 'socid':
                $this->Out[$FieldName] = self::Objects()->Encode( "ThirdParty" , $this->Object->$FieldName);
                break;

            //====================================================================//
            // Order Official Date
            case 'date':
                $this->Out[$FieldName] = !empty($this->Object->date)?dol_print_date($this->Object->date, '%Y-%m-%d'):Null;
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
            // Direct Readings
            case 'ref':
            case 'ref_client':
            case 'ref_ext':                
                $this->setSimple($FieldName,$Data);
                break;
            
            case 'ref_int':
                //====================================================================//
                //  Compare Field Data
                if ( $this->Object->$FieldName != $Data ) {
                    //====================================================================//
                    //  Update Field Data
                    $this->Object->setValueFrom($FieldName,$Data);
                    $this->needUpdate();
                }  
                break;            
            
            //====================================================================//
            // Order Company Id 
            case 'socid':
                $this->setSimple($FieldName,self::Objects()->Id( $Data ));
                break;                 
            
            //====================================================================//
            // Order Official Date
            case 'date':
                $this->setSimple('date',$Data);
                $this->setSimple('date_commande',$Data);
                break;     

            default:
                return;
        }
        unset($this->In[$FieldName]);
    }
    
}
