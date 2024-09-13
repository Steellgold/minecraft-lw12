"use client";

import { useEffect, useState } from 'react';
import { supabase } from '../db/supabase';

export const OnlineCount = () => {
  const [onlineCount, setOnlineCount] = useState(0);

  useEffect(() => {
    const fetchOnlinePlayers = async () => {
      const { data } = await supabase
        .from('Player')
        .select('isOnline, uuid')
        .eq('isOnline', true);
      
      setOnlineCount(data?.length || 0);
    };

    fetchOnlinePlayers();

    const channel = supabase
      .channel('online_count')
      .on(
        'postgres_changes',
        { event: 'UPDATE', schema: 'public', table: 'Player' },
        (payload) => {
          const { new: newData } = payload;

          if (newData.isOnline) {
            setOnlineCount((prevCount) => prevCount + 1);
          } else {
            setOnlineCount((prevCount) => prevCount - 1);
          }
        }
      )
      .subscribe();

    return () => {
      supabase.removeChannel(channel);
    };
  }, []);

  return (
    <div className="flex items-center gap-2">
      <span className="h-2 w-2 bg-green-700 rounded-full inline-block animate-pulse duration-1000"></span>
      <span className='text-muted-foreground'>{onlineCount} player{onlineCount > 1 ? 's' : ''} online</span>
    </div>
  );
};
