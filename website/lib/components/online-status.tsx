"use client";

import { useEffect, useState } from 'react';
import { supabase } from '../db/supabase';
import { Component } from '../component/component';

type OnlineStatusProps = {
  username: string;
};

export const OnlineStatus: Component<OnlineStatusProps> = ({ username }) => {
  const [isOnline, setIsOnline] = useState(false);

  useEffect(() => {
    const fetchIsOnline = async () => {
      const { data } = await supabase
        .from('Player')
        .select('isOnline, uuid')
        .eq("name", username);

      if (data && data.length > 0) {
        setIsOnline(data[0].isOnline);
      }
    };

    fetchIsOnline();

    const channel = supabase
      .channel('online_status_' + username)
      .on(
        'postgres_changes',
        { event: 'UPDATE', schema: 'public', table: 'Player' },
        (payload) => {
          const { new: newData } = payload;

          if (newData && newData.name === username && newData.isOnline !== undefined) {
            setIsOnline(newData.isOnline);
          }
        }
      )
      .subscribe();

    return () => {
      supabase.removeChannel(channel);
    };
  }, [username]);

  return (
    <>
      {isOnline ? (
        <span className="text-primary">Online</span>
      ) : (
        <span className="text-destructive">Offline</span>
      )}
    </>
  );
};
