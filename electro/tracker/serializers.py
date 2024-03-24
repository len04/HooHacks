# tracker/serializers.py
from rest_framework import serializers
from .models import MonthlyNumber

class MonthlyNumberSerializer(serializers.ModelSerializer):
    class Meta:
        model = MonthlyNumber
        fields = ['id', 'user', 'month', 'number']