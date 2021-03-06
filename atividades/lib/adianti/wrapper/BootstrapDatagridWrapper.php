<?php
namespace Adianti\Wrapper;
use Adianti\Widget\Datagrid\TDataGrid;

/**
 * Bootstrap datagrid decorator for Adianti Framework
 *
 * @version    2.0
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class BootstrapDatagridWrapper
{
    private $decorated;
    
    /**
     * Constructor method
     */
    public function __construct(TDataGrid $datagrid)
    {
        $this->decorated = $datagrid;
        $this->decorated->{'class'} = 'table table-striped table-hover';
    }
    
    /**
     * Redirect calls to decorated object
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->decorated, $method),$parameters);
    }
    
    /**
     * Shows the decorated datagrid
     */
    public function show()
    {
        $this->decorated->{'style'} = 'border-collapse:collapse';
        
        $sessions = $this->decorated->getChildren();
        if ($sessions)
        {
            foreach ($sessions as $section)
            {
                unset($section->{'class'});
                
                $rows = $section->getChildren();
                if ($rows)
                {
                    foreach ($rows as $row)
                    {
                        unset($row->{'class'});
                        $cells = $row->getChildren();
                        if ($cells)
                        {
                            foreach ($cells as $cell)
                            {
                                unset($cell->{'class'});
                            }
                        }
                    }
                }
            }
        }
        $this->decorated->show();
    }
}
