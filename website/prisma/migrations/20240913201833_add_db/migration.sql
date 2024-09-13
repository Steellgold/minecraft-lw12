-- CreateEnum
CREATE TYPE "GameStatus" AS ENUM ('STARTED', 'FINISHED');

-- CreateTable
CREATE TABLE "Player" (
    "uuid" TEXT NOT NULL,
    "name" TEXT NOT NULL,
    "head" BYTEA NOT NULL,

    CONSTRAINT "Player_pkey" PRIMARY KEY ("uuid")
);

-- CreateTable
CREATE TABLE "Game" (
    "id" TEXT NOT NULL,
    "name" TEXT NOT NULL,
    "startedAt" TIMESTAMP(3),
    "finishedAt" TIMESTAMP(3),
    "status" "GameStatus" NOT NULL,

    CONSTRAINT "Game_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "Score" (
    "id" TEXT NOT NULL,
    "score" INTEGER NOT NULL,
    "playerUuid" TEXT NOT NULL,
    "gameId" TEXT NOT NULL,

    CONSTRAINT "Score_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "_GameToPlayer" (
    "A" TEXT NOT NULL,
    "B" TEXT NOT NULL
);

-- CreateIndex
CREATE UNIQUE INDEX "Player_name_key" ON "Player"("name");

-- CreateIndex
CREATE UNIQUE INDEX "Game_name_key" ON "Game"("name");

-- CreateIndex
CREATE UNIQUE INDEX "_GameToPlayer_AB_unique" ON "_GameToPlayer"("A", "B");

-- CreateIndex
CREATE INDEX "_GameToPlayer_B_index" ON "_GameToPlayer"("B");

-- AddForeignKey
ALTER TABLE "Score" ADD CONSTRAINT "Score_playerUuid_fkey" FOREIGN KEY ("playerUuid") REFERENCES "Player"("uuid") ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "Score" ADD CONSTRAINT "Score_gameId_fkey" FOREIGN KEY ("gameId") REFERENCES "Game"("id") ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "_GameToPlayer" ADD CONSTRAINT "_GameToPlayer_A_fkey" FOREIGN KEY ("A") REFERENCES "Game"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "_GameToPlayer" ADD CONSTRAINT "_GameToPlayer_B_fkey" FOREIGN KEY ("B") REFERENCES "Player"("uuid") ON DELETE CASCADE ON UPDATE CASCADE;
