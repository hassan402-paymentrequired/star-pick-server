import React from 'react'
import { HouseWifi, Logs, CircleUser , Swords  } from 'lucide-react';
import { Link } from '@inertiajs/react';

const Nabar = () => {
  return (
    <div className='h-16 w-full py-2 px-4 border-t bottom-0 absolute grid grid-cols-4 items-center'>
      <Link href={'#'} className="flex flex-col items-center">
           <Logs size={20} color='#333' />
           <span className='text-sm '>Peers</span>
      </Link>
      <Link href={'#'} className="flex flex-col items-center">
           <Swords  size={20} color='#333' />
           <span className='text-sm '>General</span>
      </Link>
      <Link href={'#'} className="flex flex-col items-center">
           <HouseWifi size={20} color='#333' />
           <span className='text-sm '>bookings</span>
      </Link>
      <Link href={'#'} className="flex flex-col items-center">
           <CircleUser  size={20} color='#333' />
           <span className='text-sm '>Profile</span>
      </Link>
    </div>
  )
}

export default Nabar