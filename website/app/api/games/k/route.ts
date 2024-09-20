import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { prisma } from "@/lib/db/prisma";

export const POST = async (req: NextRequest): Promise<NextResponse> => {
  const apiKey = req.headers.get("x-api-key");
  if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) {
    return NextResponse.json({ error: "Invalid API key" }, { status: 401 });
  }

  const schema = z.object({
    killerUuid: z.string().uuid(),
    victimUuid: z.string().uuid(),
    gameId: z.string().optional()
  }).safeParse(await req.json());

  if (!schema.success) {
    return NextResponse.json({ error: "Invalid request body" }, { status: 400 });
  }

  const { killerUuid, victimUuid, gameId } = schema.data;

  const gameCreateResponse = await prisma.action.create({
    data: {
      killerUuid,
      victimUuid,
      game: { connect: { id: gameId } }
    }
  });

  return NextResponse.json(gameCreateResponse);
};