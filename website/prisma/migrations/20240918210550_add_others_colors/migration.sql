-- AlterEnum
-- This migration adds more than one value to an enum.
-- With PostgreSQL versions 11 and earlier, this is not possible
-- in a single migration. This can be worked around by creating
-- multiple migrations, each migration adding only one value to
-- the enum.


ALTER TYPE "Team" ADD VALUE 'YELLOW';
ALTER TYPE "Team" ADD VALUE 'GREEN';
ALTER TYPE "Team" ADD VALUE 'PURPLE';
ALTER TYPE "Team" ADD VALUE 'ORANGE';
ALTER TYPE "Team" ADD VALUE 'PINK';
ALTER TYPE "Team" ADD VALUE 'WHITE';
