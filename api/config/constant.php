<?php
//db configuration
define('DB_NAME', 'drw');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');

//Tables
define('TBL_USERS', 'tbl_users');
define('TBL_ITEM_MASTER', 'tbl_item_master');
define('TBL_PRODUCT_ITEM', 'tbl_product_item');
define('TBL_PRODUCT_MASTER', 'tbl_product_master');
define('TBL_FITTER_MASTER', 'tbl_fitter_master');
define('TBL_FITTER_PRODUCT_RATE', 'tbl_fitter_product_rate');
define('TBL_FITTER_SUPPLY', 'tbl_fitter_supply');
define('TBL_FITTER_SUPPLY_EXTRA_ITEM', 'tbl_fitter_supply_extra_item');
define('TBL_FITTER_SUPPLY_PRODUCT', 'tbl_fitter_supply_product');
define('TBL_FITTER_SUPPLY_REJECTION_ITEM', 'tbl_fitter_supply_rejection_item');
define("TBL_SKU_CREDIT", "tbl_sku_credit");
define("TBL_SKU_CREDIT_PRODUCT", "tbl_sku_credit_product");
define("TBL_SKU_CREDIT_EXTRA_ITEM", "tbl_sku_credit_extra_item");
define("TBL_SKU_CREDIT_ITEM", "tbl_sku_credit_item");

// Messages
define('MSG_LOGIN_SUCCESS', 'Login Successful!');
define('MSG_LOGIN_REQUIRED', 'Username and Password are required');
define('MSG_GET_ALL_USER_FOUND', 'Successfully get all users');
define('MSG_USER_NOT_FOUND', 'User not found');
define('MSG_LOGIN_FAILED', 'Invalid Username or Password');
define('MSG_METHOD_NOT_ALLOWED', 'Method Not Allowed');
define('MSG_USER_ALREADY_EXIST', 'Username already exists');
define('MSG_USER_ADDED', 'User added successfully');
define('MSG_USER_ADD_FAIL', 'Failed to add user');
define('MSG_USER_ID_REQUIRED', 'User ID is required');
define('MSG_REQUIRED_ALL', 'All fields are required');
define('MSG_USER_DELETE_SUCCESS', 'User deleted successfully');
define('MSG_USER_DELETE_FAIL', 'Failed to delete user');
define('MSG_USER_UPDATE_SUCCESS', 'User updated successfully');
define('MSG_USER_UPDATE_FAIL', 'Failed to update user');
define('MSG_USER_UPDATE_INVALID_FIELDS', 'Invalid fields provided for update');

define('MSG_ITEM_ADDED', 'Item added successfully.');
define('MSG_ITEM_ADD_FAIL', 'Failed to add item.');
define('MSG_ITEM_UPDATED', 'Item updated successfully.');
define('MSG_ITEM_UPDATE_FAIL', 'Failed to update item.');
define('MSG_ITEM_DELETE_SUCCESS', 'Item deleted successfully.');
define('MSG_ITEM_DELETE_FAIL', 'Failed to delete item.');
define('MSG_GET_ALL_ITEM_FOUND', 'Successfully fetched all items.');
define('MSG_ITEM_NOT_FOUND', 'No items found.');
define('MSG_ITEM_ID_REQUIRED', 'Item ID is required.');

define('MSG_PRODUCT_ADDED', 'Product added successfully');
define('MSG_PRODUCT_ADD_FAIL', 'Failed to add product');
define('MSG_PRODUCT_UPDATED', 'Product updated successfully');
define('MSG_PRODUCT_UPDATE_FAIL', 'Failed to update product');
define('MSG_PRODUCT_DELETE_SUCCESS', 'Product deleted successfully');
define('MSG_PRODUCT_DELETE_FAIL', 'Failed to delete product');
define('MSG_PRODUCT_NOT_FOUND', 'No products found');
define('MSG_GET_ALL_PRODUCTS_FOUND', 'Products retrieved successfully');
define('MSG_PRODUCT_ID_REQUIRED', 'Product ID is required');

define('MSG_RECORD_ADDED', 'Record added successfully.');
define('MSG_FAILED_TO_ADD', 'Failed to add record.');
define('MSG_RECORD_UPDATED', 'Record updated successfully.');
define('MSG_FAILED_TO_UPDATE', 'Failed to update record.');
define('MSG_RECORD_DELETED', 'Record deleted successfully.');
define('MSG_FAILED_TO_DELETE', 'Failed to delete record.');
define('MSG_RECORD_FOUND', 'Records found.');
define('MSG_RECORD_NOT_FOUND', 'No records found.');
define('MSG_MISSING_REQUIRED_FIELDS', 'Missing required fields.');
define('MSG_NO_FIELDS_TO_UPDATE', 'No fields provided for update.');
define('MSG_MISSING_ID', 'Missing ID.');

define('MSG_FITTER_PRODUCT_RATE_ADDED', 'Fitter product rate added successfully.');
define('MSG_FITTER_PRODUCT_RATE_ADD_FAIL', 'Failed to add fitter product rate.');
define('MSG_FITTER_PRODUCT_RATE_UPDATED', 'Fitter product rate updated successfully.');
define('MSG_FITTER_PRODUCT_RATE_UPDATE_FAIL', 'Failed to update fitter product rate.');
define('MSG_FITTER_PRODUCT_RATES_FOUND', 'Fitter product rates fetched successfully.');
define('MSG_FITTER_PRODUCT_RATES_NOT_FOUND', 'No fitter product rates found.');
define('MSG_FITTER_PRODUCT_RATE_DELETED', 'Fitter product rate deleted successfully.');
define('MSG_FITTER_PRODUCT_RATE_DELETE_FAIL', 'Failed to delete fitter product rate.');
define('MSG_REQUIRED_FIELDS_MISSING', 'Required fields are missing.');

define('MSG_FITTER_SUPPLY_ADDED', 'Fitter supply record added successfully.');
define('MSG_FITTER_SUPPLY_ADD_FAIL', 'Failed to add fitter supply record.');
define('MSG_FITTER_SUPPLY_UPDATED', 'Fitter supply record updated successfully.');
define('MSG_FITTER_SUPPLY_UPDATE_FAIL', 'Failed to update fitter supply record.');
define('MSG_FITTER_SUPPLY_DELETED', 'Fitter supply record deleted successfully.');
define('MSG_FITTER_SUPPLY_DELETE_FAIL', 'Failed to delete fitter supply record.');
define('MSG_FITTER_SUPPLY_NOT_FOUND', 'No fitter supply records found.');
define('MSG_GET_ALL_FITTER_SUPPLY_FOUND', 'Fitter supply records retrieved successfully.');

define("MSG_SKU_CREDIT_ADDED", "SKU Credit added successfully.");
define('MSG_SKU_CREDIT_ADD_FAIL', 'Failed to add SKU Credit record.');
define("MSG_SKU_CREDIT_UPDATED", "SKU Credit updated successfully.");
define("MSG_SKU_CREDIT_DELETED", "SKU Credit deleted successfully.");
define('MSG_SKU_CREDIT_DELETE_FAIL', 'Failed to delete SKU credit record.');
define("MSG_SKU_CREDIT_FOUND", "SKU Credit records found.");
define("MSG_SKU_CREDIT_NOT_FOUND", "No SKU Credit records found.");
define("MSG_SKU_CREDIT_UPDATE_FAIL", "Failed to update SKU Credit records.");