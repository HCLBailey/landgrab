DELETE FROM `transaction_log` WHERE created < DATE(NOW()) - INTERVAL 1 DAY;