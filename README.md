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
hosts, databases and the like. The aim is to keep your development setup as clean 
as a whistle.

Features
--------

* Manage your Drupal instances (Setup a new virtual host, a database and a host name)
* Set up from an installation profile
* Platform independent.
* Extensible
* Sync with a CVS (Git, SVN,...)

Installation
------------

1. Put the rum file base in a your /home/<user>/.drush folder
2. Configure your .drushrc file with the rum specific options. Rum will tell
   you when its' missing a crucial configuration parameter

WARNING: INSTALL AT YOUR OWN RISK! THIS IS STILL AN UNFINISHED PROJECT. Rum will
interact (add/remove) with your installation, so things might break severely.

Roadmap
-------

Rum relies on the Drush API. There are few things on the wishlist

* Each drush_COMMAND has an init, validate, pre, command and post phase. Rum doesn't
  use this although it might benefit heavily from doing so.
* Integrate better with Drush native error handling through exceptions since this
  opens up the opportunity to use drush' rollback hooks.
