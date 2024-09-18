import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/db/prisma";
import { supabase } from "@/lib/db/supabase";
import { z } from "zod";

const playerSchema = z.object({
  name: z.string().min(1, "Name is required"),
  head: z.string().min(1, "Head image is required (base64)")
});

export const POST = async (req: NextRequest): Promise<NextResponse> => {
  try {
    const apiKey = req.headers.get("x-api-key");
    if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) {
      return NextResponse.json({ error: "Invalid API key" }, { status: 401 });
    }

    const body = await req.json();
    const schema = playerSchema.safeParse(body);
    if (!schema.success) {
      return NextResponse.json({ error: "Invalid request body" }, { status: 400 });
    }

    const { name, head } = schema.data;

    const existingPlayer = await prisma.player.findUnique({
      where: { name }
    });

    if (existingPlayer) {
      return NextResponse.json({ error: "Player with this name already exists" }, { status: 409 });
    }

    const newPlayer = await prisma.player.create({
      data: {
        name,
        head: Buffer.from(head, "base64")
      }
    });

    const fileName = newPlayer.uuid + ".png";
    const { error: uploadError } = await supabase.storage.from("heads").upload(fileName, Buffer.from(head, "base64"), {
      contentType: "image/png"
    });

    if (uploadError) {
      await prisma.player.delete({ where: { uuid: newPlayer.uuid } });
      return NextResponse.json({ error: "Failed to upload image" }, { status: 500 });
    }

    return NextResponse.json(newPlayer);
  } catch (error) {
    console.error("Error creating player:", error);
    return NextResponse.json({ error: "Failed to create player" }, { status: 500 });
  }
};
