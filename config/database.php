<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'hhbk';
$db_user = 'root';
$db_pass = '';

// Global database connection variable
$db_connection = null;

/**
 * Get database connection
 * @return PDO|false
 */
function getDatabaseConnection() {
    global $db_connection, $db_host, $db_name, $db_user, $db_pass;
    
    if ($db_connection === null) {
        try {
            $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
            $db_connection = new PDO($dsn, $db_user, $db_pass);
            $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db_connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            return false;
        }
    }
    
    return $db_connection;
}

/**
 * Close database connection
 */
function closeDatabaseConnection() {
    global $db_connection;
    $db_connection = null;
}

/**
 * Execute a query and return the result
 * @param string $query
 * @param array $params
 * @return PDOStatement|false
 */
function executeQuery($query, $params = []) {
    $db = getDatabaseConnection();
    if (!$db) return false;
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("Query execution failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Get a single row from database
 * @param string $query
 * @param array $params
 * @return array|false
 */
function getSingleRow($query, $params = []) {
    $stmt = executeQuery($query, $params);
    if (!$stmt) return false;
    
    return $stmt->fetch();
}

/**
 * Get multiple rows from database
 * @param string $query
 * @param array $params
 * @return array|false
 */
function getMultipleRows($query, $params = []) {
    $stmt = executeQuery($query, $params);
    if (!$stmt) return false;
    
    return $stmt->fetchAll();
}

/**
 * Insert data and return last insert ID
 * @param string $query
 * @param array $params
 * @return int|false
 */
function insertData($query, $params = []) {
    $db = getDatabaseConnection();
    if (!$db) return false;
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $db->lastInsertId();
    } catch(PDOException $e) {
        error_log("Insert failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Update data and return affected rows
 * @param string $query
 * @param array $params
 * @return int|false
 */
function updateData($query, $params = []) {
    $stmt = executeQuery($query, $params);
    if (!$stmt) return false;
    
    return $stmt->rowCount();
}

/**
 * Delete data and return affected rows
 * @param string $query
 * @param array $params
 * @return int|false
 */
function deleteData($query, $params = []) {
    $stmt = executeQuery($query, $params);
    if (!$stmt) return false;
    
    return $stmt->rowCount();
}

/**
 * Count rows from a query
 * @param string $query
 * @param array $params
 * @return int|false
 */
function countRows($query, $params = []) {
    $stmt = executeQuery($query, $params);
    if (!$stmt) return false;
    
    return $stmt->fetchColumn();
}
?> 