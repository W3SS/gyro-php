<?php
/**
 * Tokens DAO class
 */
class DAOTokens extends DataObjectBase {
	/**
	 * Create the table object describing this dataobejcts table
	 */
	protected function create_table_object() {
		return new DBTable(
			'tokens',
			array(
				new DBFieldText('token', 40, null, DBField::NOT_NULL),
				new DBFieldDateTime('creationdate', DBFieldDateTime::NOW, DBFieldDateTime::TIMESTAMP | DBField::NOT_NULL)
			),
			'token'
		);		
	}
	
}