<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12/31/2009 0:51
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_categories ORDER BY sort ASC';
$list_cats = $nv_Cache->db( $sql, 'id', $module_name );

/**
 * GetCatidInParent()
 *
 * @param mixed $catid
 * @return
 */
function GetCatidInParent( $catid )
{
	global $list_cats;

	$array_cat = array();
	$array_cat[] = $catid;
	$subcatid = explode( ',', $list_cats[$catid]['subcatid'] );

	if( ! empty( $subcatid ) )
	{
		foreach( $subcatid as $id )
		{
			if( $id > 0 )
			{
				if( $list_cats[$id]['numsubcat'] == 0 )
				{
					$array_cat[] = (int)$id;
				}
				else
				{
					$array_cat_temp = GetCatidInParent( $id );
					foreach( $array_cat_temp as $catid_i )
					{
						$array_cat[] = (int)$catid_i;
					}
				}
			}
		}
	}
	return array_unique( $array_cat );
}