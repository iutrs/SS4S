SS4S Self Service for Students
========================

SS4S is a application designed for serving students (mostly CS students).
Through plugins, they can freely access several services, to create databases, 
Virtual machines, web storage... The number of creations, the type of databases
are controlled directly in the application.

The application is designed to authenticate users with the Central
Authentication Service (also known as CAS server). Authorization is made with
LDAP groups, (currently checked in Active Directory)

1) Install
----------------------------------

This is a Symfony2 application so basic installations applies. After checking
out the repository, rename all dist files, and configure them:

    app/config/parameters.yml.dist
    app/config/config.yml.dist

After that, do a :

    php composer.phar install
    
2) Configuration of Core and Plugins
----------------------------------

All services are panaged by plugins only. The Core of the applications is only
here to do general actions (Authentication, Authorization ...)

For specifing the super administrator, you have to edit the database directly
(fix needed), after having loggued once in the application.


3) Plugin Mysql
----------------------------------
After configuring the plugin from the web interface, you'll be able to look the
current configuration here:

    src/Ss4s/Plugins/MySQLBundle/Resources/config/args.yml

4) Plugin Storage
----------------------------------
Basic plugin

