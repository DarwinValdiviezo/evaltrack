import { Module } from '@nestjs/common';
import { AttendancesService } from './attendances.service';
import { AttendancesController } from './attendances.controller';
import { PrismaService } from '../prisma.service';

@Module({
  providers: [AttendancesService, PrismaService],
  controllers: [AttendancesController],
  exports: [AttendancesService],
})
export class AttendancesModule {}
