Simple Data Access Control
=====================

Simple Data Access Control allows the restriction of which user can access which resources.

Access Privileges are granted either to users directly or to roles, applying to all users who have that specific role.

Privileges are given per resource, so that in order to remove the write access to all items within a class, the new access rights need to be applied recursively to all resources by checking "recursive" before saving the changes.

Privileges are additive, meaning that if:

* Role A has write and read access to Item 1
* User X has read access to Item 1
* And User X has the Role A

Then User X he will have read and write access to Item 1


