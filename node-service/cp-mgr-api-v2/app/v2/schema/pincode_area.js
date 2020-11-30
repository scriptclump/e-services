/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('pincode_area', {
    pincode_area_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    pjp_pincode_area_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'pjp_pincode_area',
        key: 'pjp_pincode_area_id'
      }
    },
    pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    area_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'pincode_area'
  });
};
