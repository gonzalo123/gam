CREATE TABLE "tblNotes" ("id" INTEGER PRIMARY KEY  NOT NULL , "title" VARCHAR, "body" VARCHAR);
CREATE TABLE "tblPlaces" ("id" INTEGER PRIMARY KEY  NOT NULL , "lat" DOUBLE, "lng" DOUBLE, "title" VARCHAR, "body" VARCHAR);
CREATE TABLE "tblbooks" ("id" INTEGER PRIMARY KEY  NOT NULL ,"title" VARCHAR,"author" VARCHAR,"bookyear" INTEGER,"readdate" DATETIME,"status" INTEGER,"why" VARCHAR,"valoration" VARCHAR,"stars" INTEGER, "asin" INTEGER);
CREATE TABLE [users] (
[id] INTEGER  NOT NULL PRIMARY KEY, 
[username] VARCHAR(50) UNIQUE NOT NULL, 
[password] VARCHAR(32) NULL, 
[real_name] VARCHAR(150) NULL);
