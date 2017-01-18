.. _theme_it_communities:

IT Communities Sub-Theme
========================

The custom IT Communities theme is a sub-theme based on the :ref:`theme_wfp_base`.

.. attention::

  - There are several pieces of business logic which are handled in this theme which should moved into the custom Features instead.
  - In some cases, the core Drupal theme regions have been used rather than using the Context module to manage the content layouts.
  - Some content type template files are identical and redundant, the logic of selecting these template files should be altered to use just one template file.

JavaScript libraries
--------------------

The theme uses the following 3rd party JavaScript libraries:

- html5shiv.js
