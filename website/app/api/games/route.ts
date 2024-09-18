import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { supabase } from "@/lib/db/supabase";
import { prisma } from "@/lib/db/prisma";

const gameSchema = z.object({
  playerUuids: z.array(z.string()).min(2, "At least 2 players are required")
});

export const POST = async (req: NextRequest): Promise<NextResponse> => {
  const apiKey = req.headers.get("x-api-key");
  if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) {
    return NextResponse.json({ error: "Invalid API key" }, { status: 401 });
  }

  const body = await req.json();
  const schema = gameSchema.safeParse(body);

  if (!schema.success) {
    return NextResponse.json({ error: "Invalid request body" }, { status: 400 });
  }

  const { playerUuids } = schema.data;

  const playersResponse = await supabase
    .from("Player")
    .select("*")
    .in("uuid", playerUuids);

  if (!playersResponse || !playersResponse.data || playersResponse.data.length !== playerUuids.length) {
    return NextResponse.json({ error: "One or more players not found" }, { status: 404 });
  }

  const teamsByPlayer: Record<string, "RED" | "BLUE"> = playerUuids.reduce((acc, uuid, index) => {
    const team = index % 2 === 0 ? "RED" : "BLUE";
    return { ...acc, [uuid]: team };
  }, {});

  const data = await prisma.game.create({
    data: {
      players: {
        connect: playersResponse.data.map((player) => ({
          uuid: player.uuid
        }))
      },
      scores: {
        createMany: {
          data: playerUuids.map((uuid) => ({
            deathCount: 0,
            score: 0,
            team: teamsByPlayer[uuid],
            playerUuid: uuid
          }))
        }
      },
      status: "STARTED"
    }
  });

  console.log(data);

  return NextResponse.json(data);
};

export const PUT = async (req: NextRequest): Promise<NextResponse> => {
  const apiKey = req.headers.get("x-api-key");
  if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) {
    return NextResponse.json({ error: "Invalid API key" }, { status: 401 });
  }

  const body = await req.json();

  const { gameId, playerUuid, score, deathCount } = body;

  if (!gameId || !playerUuid || !score || !deathCount) {
    return NextResponse.json({ error: "Missing required fields" }, { status: 400 });
  }

  const game = await prisma.game.findUnique({
    where: {
      id: gameId
    },
    include: {
      scores: true
    }
  });

  if (!game) {
    return NextResponse.json({ error: "Game not found" }, { status: 404 });
  }

  const scoreToUpdate = game.scores.find((s) => s.playerUuid === playerUuid);

  if (!scoreToUpdate) {
    return NextResponse.json({ error: "Player not found in game" }, { status: 404 });
  }

  const updatedScore = await prisma.score.update({
    where: {
      id: scoreToUpdate.id
    },
    data: {
      deathCount,
      score
    }
  });
  
  return NextResponse.json(updatedScore);
}

export const PATCH = async (req: NextRequest): Promise<NextResponse> => {
  const apiKey = req.headers.get("x-api-key");
  if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) {
    return NextResponse.json({ error: "Invalid API key" }, { status: 401 });
  }

  const body = await req.json();
  const { gameId } = body;

  if (!gameId) {
    return NextResponse.json({ error: "Missing required fields" }, { status: 400 });
  }

  const game = await prisma.game.update({
    where: {
      id: gameId
    },
    data: {
      status: "FINISHED"
    }
  });

  return NextResponse.json(game);
}