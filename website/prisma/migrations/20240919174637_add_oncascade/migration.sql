-- DropForeignKey
ALTER TABLE "Score" DROP CONSTRAINT "Score_gameId_fkey";

-- AddForeignKey
ALTER TABLE "Score" ADD CONSTRAINT "Score_gameId_fkey" FOREIGN KEY ("gameId") REFERENCES "Game"("id") ON DELETE CASCADE ON UPDATE CASCADE;
