/*
  Warnings:

  - A unique constraint covering the columns `[simpleId]` on the table `Game` will be added. If there are existing duplicate values, this will fail.

*/
-- CreateEnum
CREATE TYPE "Team" AS ENUM ('RED', 'BLUE');

-- AlterTable
ALTER TABLE "Game" ADD COLUMN     "simpleId" SERIAL NOT NULL;

-- AlterTable
ALTER TABLE "Score" ADD COLUMN     "team" "Team" NOT NULL DEFAULT 'RED';

-- CreateIndex
CREATE UNIQUE INDEX "Game_simpleId_key" ON "Game"("simpleId");
