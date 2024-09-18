import { prisma } from "@/lib/db/prisma";
import { supabase } from "@/lib/db/supabase";
import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

export const PUT = async({ url, headers }: NextRequest): Promise<NextResponse> => {
  const apiKey = headers.get("x-api-key");
  if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) return NextResponse.json({ error: "Invalid API key" }, { status: 401 });

  const playerName = new URL(url).pathname.split("/")[2];
  const schema = z.string().safeParse(playerName);
  if (!schema.success) return NextResponse.json({ error: "Invalid player" }, { status: 400 });

  const data = await supabase.from("Player")
    .select("*")
    .eq("name", playerName)
    .limit(1);

  if (!data) return NextResponse.json({ error: "Player not found" }, { status: 404 });
  if (!data.data) return NextResponse.json({ error: "Player not found" }, { status: 404 });

  await prisma.$executeRaw`UPDATE "Player" SET "isOnline" = false WHERE "uuid" = ${data.data[0].uuid}`; // Idk why with Supabase have a "No content"
  return NextResponse.json({ isOnline: false });
}