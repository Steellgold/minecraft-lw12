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

  const data = await prisma.game.create({
    data: {
      players: {
        connect: playersResponse.data.map((player) => ({
          uuid: player.uuid
        }))
      },
      status: "STARTED"
    }
  });

  console.log(data);

  return NextResponse.json(data);
};
