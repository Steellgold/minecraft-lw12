/*
  Warnings:

  - You are about to drop the column `name` on the `Game` table. All the data in the column will be lost.

*/
-- DropIndex
DROP INDEX "Game_name_key";

-- AlterTable
ALTER TABLE "Game" DROP COLUMN "name";
