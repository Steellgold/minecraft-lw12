import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { prisma } from "@/lib/db/prisma";

const gameSchema = z.object({
  players: z.array(z.object({
    uuid: z.string().uuid(),
    team: z.enum(["RED", "BLUE"])
  }))
});

export const POST = async (req: NextRequest): Promise<NextResponse> => {
  const apiKey = req.headers.get("x-api-key");
  if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) {
    return NextResponse.json({ error: "Invalid API key" }, { status: 401 });
  }

  const body = await req.json();
  const schema = gameSchema.safeParse(body);
  if (!schema.success) {
    console.log(schema.error);
    return NextResponse.json({ error: "Invalid request body" }, { status: 400 });
  }

  const { players } = schema.data;

  const gameCreateResponse = await prisma.game.create({
    data: {
      players: {
        connect: players.map((player) => ({ uuid: player.uuid }))
      },
      scores: {
        createMany: {
          data: players.map((player) => ({
            team: player.team,
            deathCount: 0,
            score: 0,
            playerUuid: player.uuid
          }))
        }
      },
      status: "STARTED"
    }}
  );

  return NextResponse.json(gameCreateResponse);
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