import { prisma } from "@/lib/db/prisma";
import { Player } from "@prisma/client";
import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

export const GET = async({ url }: NextRequest): Promise<NextResponse> => {
  const playerName = new URL(url).pathname.split("/")[2];
  const schema = z.string().safeParse(playerName);
  if (!schema.success) return NextResponse.json({ error: "Invalid player" }, { status: 400 });

  const data = await prisma.$queryRaw<Player[]>`SELECT * FROM "Player" WHERE name = ${playerName}`;
  if (!data) return NextResponse.json({ error: "Player not found" }, { status: 404 });

  return new NextResponse(data[0].head, { headers: { 'content-type': 'image/png' } });
}

export const PATCH = async(res: NextRequest): Promise<NextResponse> => {
  const apiKey = res.headers.get("x-api-key");
  if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) return NextResponse.json({ error: "Invalid API key" }, { status: 401 });
  
  const playerName = new URL(res.url).pathname.split("/")[2];
  const schema = z.string().safeParse(playerName);
  if (!schema.success) return NextResponse.json({ error: "Invalid player" }, { status: 400 });

  const data = await prisma.$queryRaw<Player[]>`SELECT * FROM "Player" WHERE name = ${playerName}`;
  if (!data) return NextResponse.json({ error: "Player not found" }, { status: 404 });

  const bodySchema = z.object({ head: z.string() }).safeParse(await res.json());
  if (!bodySchema.success) {
    console.log(bodySchema.error);
    return NextResponse.json({ error: "Invalid body" }, { status: 400 });
  }

  await prisma.$executeRaw`UPDATE "Player" SET head = ${Buffer.from(bodySchema.data.head, "base64")} WHERE name = ${playerName}`;
  return NextResponse.json({ message: "Head updated" });
}