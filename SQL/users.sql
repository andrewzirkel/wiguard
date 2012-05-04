CREATE USER `radius`@'localhost' IDENTIFIED BY 'radius';
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP on radius.* TO `radius`@'localhost';
CREATE USER `wiguard`@'localhost' IDENTIFIED BY 'wiguard';
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP on wiguard.* TO `wiguard`@'localhost';
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP on radius.* TO `wiguard`@'localhost';
