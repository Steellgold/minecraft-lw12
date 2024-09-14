/*
  Warnings:

  - Added the required column `deathCount` to the `Score` table without a default value. This is not possible if the table is not empty.

*/
-- AlterTable
ALTER TABLE "Game" ALTER COLUMN "startedAt" SET DEFAULT CURRENT_TIMESTAMP;

-- AlterTable
ALTER TABLE "Score" ADD COLUMN     "deathCount" INTEGER NOT NULL;
