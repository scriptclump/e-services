/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('aggrement_terms', {
    terms_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'legal_entities',
        key: 'legal_entity_id'
      }
    },
    le_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    vendor_reg_charges: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sku_reg_charges: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    dc_link_charges: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    b2b_channel_support_as: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ecp_visibility_ass: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    po_days: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    delivery_tat: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    delivery_tat_uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    invoice_days: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    delivery_frequency: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    credit_period: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    payment_days: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    negotiation: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    rtv: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    rtv_timeline: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    rtv_scope: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    rtv_location: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    start_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    end_date: {
      type: DataTypes.DATEONLY,
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
    tableName: 'aggrement_terms'
  });
};
