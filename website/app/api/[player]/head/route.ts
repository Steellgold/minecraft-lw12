import { supabase } from "@/lib/db/supabase";
import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

export const GET = async({ url }: NextRequest): Promise<NextResponse> => {
  const playerName = new URL(url).pathname.split("/")[2];
  const schema = z.string().safeParse(playerName);
  if (!schema.success) return NextResponse.json({ error: "Invalid player" }, { status: 400 });

  const data = await supabase.from("Player")
    .select("*")
    .eq("name", playerName)
    .limit(1);

  if (!data) return NextResponse.json({ error: "Player not found" }, { status: 404 });
  if (!data.data) return NextResponse.json({ error: "Player not found" }, { status: 404 });

  const head = supabase.storage.from("heads").getPublicUrl(data?.data[0].uuid + ".png");
  return NextResponse.json({ url: head?.data.publicUrl });
}

export const PATCH = async(res: NextRequest): Promise<NextResponse> => {
  const apiKey = res.headers.get("x-api-key");
  if (apiKey !== process.env.SUPER_SECRET_API_MEGA_KEY) return NextResponse.json({ error: "Invalid API key" }, { status: 401 });
  
  const playerName = new URL(res.url).pathname.split("/")[2];
  const schema = z.string().safeParse(playerName);
  if (!schema.success) return NextResponse.json({ error: "Invalid player" }, { status: 400 });

  const data = await supabase.from("Player").select("*").eq("name", playerName);
  if (!data) return NextResponse.json({ error: "Player not found" }, { status: 404 });
  if (!data.data) return NextResponse.json({ error: "Player not found" }, { status: 404 });

  const bodySchema = z.object({ head: z.string() }).safeParse(await res.json());
  if (!bodySchema.success) return NextResponse.json({ error: "Invalid body" }, { status: 400 });

  const fileName = data?.data[0].uuid + ".png";

  const head = supabase.storage.from("heads").getPublicUrl(fileName);
  if (head) {
    const deleteHead = await supabase.storage.from("heads").remove([fileName]);
    if (deleteHead.error) return NextResponse.json({ error: "Failed to delete old head" }, { status: 500 });
  }

  await supabase.storage.from("heads").upload(fileName, Buffer.from(bodySchema.data.head, "base64"), { contentType: "image/png" });
  return NextResponse.json({ success: true });
}