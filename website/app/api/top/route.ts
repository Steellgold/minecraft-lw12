import { prisma } from "@/lib/db/prisma";
import { supabase } from "@/lib/db/supabase";
import { NextResponse } from "next/server";

export const GET = async (): Promise<NextResponse> => {
  try {
    const players = await prisma.player.findMany({
      include: {
        score: true,
      },
    });

    const playersData = await Promise.all(players.map(async (player) => {
      const totalScore = player.score.reduce((acc, curr) => acc + curr.score, 0);
      const totalDeaths = player.score.reduce((acc, curr) => acc + curr.deathCount, 0);

      const head = supabase.storage.from("heads").getPublicUrl(player.uuid + ".png");

      return {
        username: player.name,
        headUrl: head.data?.publicUrl || null,
        totalScore,
        totalDeaths,
      };
    }));

    const sortedPlayers = playersData.sort((a, b) => {
      if (b.totalScore === a.totalScore) {
        return a.totalDeaths - b.totalDeaths;
      }
      return b.totalScore - a.totalScore;
    });

    return NextResponse.json(sortedPlayers);
  } catch (error) {
    console.error("Error fetching players:", error);
    return NextResponse.json({ error: "Failed to fetch players" }, { status: 500 });
  }
};
