"use server";

import { supabase } from "@/lib/db/supabase";

export const searchAction = async(username: string, formData: FormData) => {
  if (!username) return { username: "", headUrl: "", isOnline: false };

  const data = await supabase.from("Player").select("*").eq("name", username);
  if (data.error) return { error: "Failed to fetch" };

  if (!data.data.length) return { username: "", headUrl: "", isOnline: false, error: "Player not found" };

  const imageRequest = await fetch(process.env.URL + "api/" + username + "/head", {
    method: "GET",
  }).then((res) => res.json());

  return {
    username,
    headUrl: imageRequest.url,
    isOnline: data.data[0].isOnline
  };
}