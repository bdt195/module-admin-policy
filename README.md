# TonyB_AdminPolicy module 1.0.0.

## Overview

This module customizes the policy for admin password.

### Features

- Password history: 24 passwords remembered. Password validation will be checked to make sure that the new password is not included in 24 recently one.

- Add custom policy for admin password:
  - Minimum length 8 characters.
  - Passwords cannot contain the user's account name or parts of the user's full name that exceed two consecutive characters.
  - Passwords must contain characters from three of the following four categories:
    - English uppercase characters (A through Z).
    - English lowercase characters (a through z).
    - Base 10 digits (0 through 9).
    - Non-alphabetic characters (for example, ! @ # & ( ) – [ { } ] : ; ', ? / * ` ~ $ ^ + = < > “)

## Required modules

* Magento_User

## Configuration

* No specified configuration options for this module.
