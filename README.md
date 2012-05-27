README
======

Author
------

Matthias "Netsensei" Vandermaesen
matthias@colada.be
http://www.colada.be

What is Rum?
------------

Rum is a set of Drush scripts that makes a Drupal developers' life easier.
It allows you to quickly set up and tear down Drupal websites with as few
commands as possible. Rum takes away the pain of tediously managing virtual
hosts, databases,... 

The aim is to keep your development setup as clean as a whistle and allowing you
to set up a new Drupal site quickly.

Why Rum?
--------

Although a myriad of general solutions already exists, the idea behind Rum is to 
do one thing only: quickly setting up and tearing down Drupal websites.

Rum is also a learning experience. I wanted to learn more about Drush and I wanted
to really dive into OOP and design patterns.

WARNING: INSTALL AT YOUR OWN RISK! THIS IS STILL AN UNFINISHED PROJECT. Rum will
interact (add/remove) with your installation, so things might break severely.

Features
--------

* Manage your Drupal instances (Setup a new virtual host, a database and a host name)
* Set up Drupal vanilla
* Install a project from a CVS (Git)
* Platform independent (MAMP, Ubuntu,...)

Installation
------------

1. Download and put the rum installation base in a your /Users/<code>username</code>/.drush folder, or clone the repository from Github:

		cd .drush
		git clone git://github.com/netsensei/Rum.git
		
2. Configure your .drushrc file with the rum specific options contained in the drushrc.php file
   in the installation base. Rum will tell you when it is missing a crucial configuration parameter

How to use Rum
--------------

Install a vanilla Drupal project called foobar

    ~ drush rc vanilla foobar

Install a project Foobar from a CVS repository (local or remote)

    ~ drush rc repository foobar

Both commands will create a domain name called hostname.foobar. Navigate to
http://hostname.foobar to see your project.

Remove a project (vhost, folders, link, database,...):

    ~ drush rd foobar

Where does Rum store my data?
-----------------------------

There is a whole range of ways to configure a L/MAMP stack. Some prefer to put their
document roots in /var/www, others link to a different directory. Same goes for managing
database dumps, vhost configurations,...

Rum isn't designed to cater in a flexible way with different setups. The primary goal
is to go for a few setups and get things up quick and simple.

Rum will create a **workspace** directory. All your projects will be stored in this
directory. Each project resides in its own **project directory**

Each project directory ideally has these two directories: a **web directory** (i.e. www)
and a **database directory** (i.e. db) The first will act as a document root to your
vhost configuration, the second contains all your database dumps.

For each virtual host, Rum will create a separate file in the vhost configuration directory.

For each project, Rum will create a single database and generate a settings file which will
connect it to the project.

It's possible to change the defaults in your .drushrc.php configuration and or pass specific
changes as options from the command line. Refer to this command for more information:

   ~ drush rc help

Roadmap
-------

Rum relies on the Drush API. There are few things on the wishlist

* Integrate better with Drush native error handling through exceptions since this
  opens up the opportunity to use drush' rollback hooks.
* Better use of native Drush API functions. I've written some stuff which was already
  in the API.
* Create projects from an installation profile
* CVS: Include support for Subversion
* Web: Include support for NGinX
* DB: Include support for pgsql