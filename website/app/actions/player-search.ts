"use server";

import { supabase } from "@/lib/db/supabase";

export const searchAction = async (username: string) => {
  if (!username) return { username: "", headUrl: "", isOnline: false };

  const { data: playerData, error: playerError } = await supabase
    .from("Player")
    .select("*")
    .eq("name", username)
    .single();

  if (playerError || !playerData) return { error: "Player not found", username: "", headUrl: "", isOnline: false };

  const { data: gamesData, error: gamesError } = await supabase
    .from("_GameToPlayer")
    .select("*")
    .eq("B", playerData.uuid);

  if (gamesError || !gamesData || gamesData.length === 0) return { error: "No games found", username: "", headUrl: "", isOnline: false };

  const { data: scoresData, error: scoresError } = await supabase
    .from("Score")
    .select("*")
    .in(
      "gameId",
      gamesData.map((game) => game.A)
    )
    .eq("playerUuid", playerData.uuid);

  if (scoresError || !scoresData || scoresData.length === 0) return { error: "No scores found", username: "", headUrl: "", isOnline: false };

  const imageResponse = await fetch(`${process.env.URL}api/${username}/head`);
  const imageRequest = await imageResponse.json();

  const games = await Promise.all(
    gamesData.map(async (game) => {
      const { data: gameData, error: gameDataError } = await supabase
        .from("Game")
        .select("startedAt,simpleId,status")
        .eq("id", game.A)
        .single();
      if (gameDataError || !gameData) return { error: "Game data not found" };

      const { data: gamePlayersData, error: gamePlayersError } = await supabase.from("_GameToPlayer").select("*").eq("A", game.A);
      if (gamePlayersError || !gamePlayersData || gamePlayersData.length === 0) return { error: "No players found" };

      const { data: gameScoresData, error: gameScoresError } = await supabase
        .from("Score")
        .select("*")
        .eq("gameId", game.A)
        .in("playerUuid", gamePlayersData.map((player) => player.B));

      if (gameScoresError || !gameScoresData || gameScoresData.length === 0) return { error: "No scores found" };

      const { data: gameActionsData, error: gameActionsError } = await supabase
        .from("Action")
        .select("*")
        .eq("gameId", game.A);
      
      if (gameActionsError || !gameActionsData || gameActionsData.length === 0) return { error: "No actions found" };

      const players = await Promise.all(
        gamePlayersData.map(async (player) => {
          const { data: playerInfo, error: playerInfoError } = await supabase
            .from("Player")
            .select("*")
            .eq("uuid", player.B)
            .single();

          if (playerInfoError || !playerInfo) return { error: "Player info not found" };

          const headResponse = await fetch(`${process.env.URL}api/${playerInfo.name}/head`);
          const headData = await headResponse.json();

          const playerScoreData = gameScoresData.find(
            (score) => score.playerUuid === player.B
          );

          return {
            uuid: player.B,
            username: playerInfo.name,
            team: playerScoreData ? playerScoreData.team : "ERROR",
            headUrl: headData.url,
            score: playerScoreData ? playerScoreData.score : 0,
            deaths: playerScoreData ? playerScoreData.deathCount : 0,
          };
        })
      );

      return {
        gameId: game.A,
        simpleId: gameData.simpleId,
        startedAt: gameData.startedAt,
        status: gameData.status,
        players,
        actions: gameActionsData,
      };
    })
  );

  const totalDeaths = scoresData.reduce((acc, score) => acc + score.deathCount, 0);
  const totalScore = scoresData.reduce((acc, score) => acc + score.score, 0);

  games.sort((a, b) => new Date(b.startedAt ?? new Date()).getTime() - new Date(a.startedAt ?? new Date()).getTime());

  return {
    username,
    headUrl: imageRequest.url,
    isOnline: playerData.isOnline,
    nbrGames: gamesData.length,
    deaths: totalDeaths,
    score: totalScore,
    games,
  };
};